<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * SippApi — Library Service untuk integrasi API SIPP mediasi
 */
class SippApi {

    protected $CI;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->model('M_pengaturan');
        $this->CI->load->model('M_perkara');
        $this->CI->load->model('M_jenis_perkara');
        $this->CI->load->model('M_mediator');
        $this->CI->load->model('M_user');
    }

    /**
     * Melakukan HTTP request cURL ke API SIPP
     */
    public function fetch_data($params = [], $override_url = null, $override_key = null) {
        $settings = $this->CI->M_pengaturan->get_all_as_array();
        $api_url  = !empty($override_url) ? trim($override_url) : (!empty($settings['api_mediasi_url']) ? trim($settings['api_mediasi_url']) : 'http://192.168.100.5/perkara360/api/mediasi');
        $api_key  = ($override_key !== null) ? trim($override_key) : (!empty($settings['api_mediasi_key']) ? trim($settings['api_mediasi_key']) : '');

        if (!empty($params)) {
            $query = http_build_query($params);
            $api_url .= (strpos($api_url, '?') !== false ? '&' : '?') . $query;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $headers = [
            'Accept: application/json'
        ];
        if (!empty($api_key)) {
            $headers[] = 'X-API-KEY: ' . $api_key;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response  = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error     = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return [
                'status'    => 'error',
                'message'   => 'Gagal terhubung ke API SIPP: ' . $error,
                'http_code' => $http_code,
                'data'      => []
            ];
        }

        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status'    => 'error',
                'message'   => 'Format respon API SIPP tidak valid (Bukan JSON). HTTP Code: ' . $http_code,
                'raw'       => substr($response, 0, 500),
                'http_code' => $http_code,
                'data'      => []
            ];
        }

        if (!isset($result['http_code'])) {
            $result['http_code'] = $http_code;
        }

        return $result;
    }

    /**
     * Memeriksa tes koneksi ke API SIPP
     */
    public function test_connection($url = null, $key = null) {
        $res = $this->fetch_data([], $url, $key);

        if (isset($res['status']) && $res['status'] === 'error') {
            return [
                'status'  => 'error',
                'message' => 'Tes Koneksi Gagal: ' . $res['message']
            ];
        }

        $count = isset($res['count']) ? (int)$res['count'] : (isset($res['data']) && is_array($res['data']) ? count($res['data']) : 0);
        $msg   = isset($res['message']) ? $res['message'] : 'API SIPP merespon dengan baik.';

        return [
            'status'    => 'success',
            'message'   => 'Koneksi Berhasil! API SIPP terhubung. (' . $msg . ' - Total data: ' . $count . ')',
            'count'     => $count,
            'http_code' => isset($res['http_code']) ? $res['http_code'] : 200
        ];
    }

    /**
     * Memproses sinkronisasi data dari API SIPP ke mediasi_db
     */
    public function sync($params = [], $override_url = null, $override_key = null) {
        $api_res = $this->fetch_data($params, $override_url, $override_key);

        if (isset($api_res['status']) && $api_res['status'] === 'error') {
            return $api_res;
        }

        $items = isset($api_res['data']) && is_array($api_res['data']) ? $api_res['data'] : [];
        if (empty($items)) {
            return [
                'status'  => 'success',
                'message' => 'Sinkronisasi selesai. Tidak ada data perkara ditemukan dari API.',
                'count'   => 0,
                'sync'    => ['inserted' => 0, 'updated' => 0, 'skipped' => 0, 'failed' => 0]
            ];
        }

        $settings = $this->CI->M_pengaturan->get_all_as_array();
        $batas_hari = !empty($settings['batas_waktu_mediasi_hari']) ? (int)$settings['batas_waktu_mediasi_hari'] : 30;

        $inserted = 0;
        $updated  = 0;
        $skipped  = 0;
        $failed   = 0;
        $details  = [];

        foreach ($items as $item) {
            if (empty($item['nomor_perkara'])) {
                $failed++;
                continue;
            }

            $this->CI->db->trans_begin();

            try {
                $nomor_perkara = trim($item['nomor_perkara']);

                // 1. Match / Insert Jenis Perkara
                $jenis_nama = !empty($item['jenis_perkara']) ? trim($item['jenis_perkara']) : 'Lainnya';
                $jenis_row  = $this->CI->db->get_where('jenis_perkara', ['nama' => $jenis_nama])->row();
                if (!$jenis_row) {
                    $this->CI->db->insert('jenis_perkara', [
                        'nama'       => $jenis_nama,
                        'keterangan' => 'Otomatis diimpor dari API SIPP',
                        'is_active'  => 1
                    ]);
                    $jenis_perkara_id = $this->CI->db->insert_id();
                } else {
                    $jenis_perkara_id = $jenis_row->id;
                }

                // 2. Match / Find Panitera Pengganti (pp_id)
                $pp_id = $this->resolve_pp_id($item);

                // 3. Majelis Hakim Parsing
                $majelis_raw = !empty($item['majelis_hakim']) ? $item['majelis_hakim'] : '';
                $clean_hakim = $this->clean_majelis_text($majelis_raw);

                // 4. Tanggal Penetapan & Batas Mediasi
                $tgl_penetapan = !empty($item['tanggal_penetapan_mediator']) ? date('Y-m-d', strtotime($item['tanggal_penetapan_mediator'])) : null;
                $tgl_batas     = $tgl_penetapan ? date('Y-m-d', strtotime($tgl_penetapan . " +{$batas_hari} days")) : date('Y-m-d', strtotime("+{$batas_hari} days"));

                // Pengecekan Perkara Eksisting & Penunjukan Mediator di perkara_mediator
                $existing    = $this->CI->db->get_where('perkara', ['nomor_perkara' => $nomor_perkara])->row();
                $existing_pm = $existing ? $this->CI->db->get_where('perkara_mediator', ['perkara_id' => $existing->id, 'is_active' => 1])->row() : null;

                // PENGECEKAN UTAMA: Jika Nomor Perkara & Tanggal Penetapan Mediator (di perkara_mediator) SAMA -> SKIP RE-SYNC!
                if ($existing && $existing_pm && !empty($existing_pm->tgl_penetapan) && $tgl_penetapan && $existing_pm->tgl_penetapan === $tgl_penetapan) {
                    $this->CI->db->trans_rollback();
                    $skipped++;
                    $details[] = "Perkara {$nomor_perkara} (Penetapan: {$tgl_penetapan}) tidak berubah (di-skip).";
                    continue;
                }

                $data_perkara = [
                    'nomor_perkara'     => $nomor_perkara,
                    'perkara_id_sipp'   => isset($item['perkara_id']) ? $item['perkara_id'] : null,
                    'jenis_perkara_id'  => $jenis_perkara_id,
                    'majelis_hakim'     => $clean_hakim,
                    'majelis_id'        => isset($item['majelis_id']) ? $item['majelis_id'] : null,
                    'panitera_sidang'   => isset($item['panitera_sidang']) ? $item['panitera_sidang'] : null,
                    'tgl_batas_mediasi' => $tgl_batas,
                    'pp_id'             => $pp_id,
                ];

                if ($existing) {
                    $perkara_id = $existing->id;
                    $this->CI->db->where('id', $perkara_id)->update('perkara', $data_perkara);
                    $updated++;
                } else {
                    $data_perkara['status'] = 'menunggu';
                    $this->CI->db->insert('perkara', $data_perkara);
                    $perkara_id = $this->CI->db->insert_id();
                    $inserted++;
                }

                // 5. Handling Mediator dari API (jika ada)
                if (!empty($item['nama_mediator'])) {
                    $this->process_mediator_assignment($perkara_id, $item, $pp_id);
                }

                // 6. Handling Parsing Pihak & Kuasa Hukum secara Smart Upsert (Tanpa Delete)
                $this->process_pihak_parsing($perkara_id, $item);

                if ($this->CI->db->trans_status() === FALSE) {
                    $this->CI->db->trans_rollback();
                    $failed++;
                    log_message('error', "Gagal sync perkara {$nomor_perkara}: Transaction status FALSE");
                } else {
                    $this->CI->db->trans_commit();
                    $details[] = "Perkara {$nomor_perkara} berhasil diproses.";
                }

            } catch (Exception $e) {
                $this->CI->db->trans_rollback();
                $failed++;
                log_message('error', "Gagal sync perkara {$item['nomor_perkara']}: " . $e->getMessage());
            }
        }

        // Update timestamp last sync
        $this->CI->db->where('setting_key', 'api_last_sync')->update('settings', ['setting_value' => date('Y-m-d H:i:s')]);

        return [
            'status'  => 'success',
            'message' => "Sinkronisasi SIPP API selesai: {$inserted} baru, {$updated} diperbarui, {$failed} gagal.",
            'count'   => count($items),
            'sync'    => [
                'inserted' => $inserted,
                'updated'  => $updated,
                'skipped'  => $skipped,
                'failed'   => $failed
            ],
            'details' => $details
        ];
    }

    /**
     * Resolusi ID PP dari data API ke tabel users
     */
    private function resolve_pp_id($item) {
        $pp_name = '';
        if (!empty($item['panitera_sidang'])) {
            $pp_name = preg_replace('/^Panitera Sidang:\s*/i', '', $item['panitera_sidang']);
        }

        if (!empty($pp_name)) {
            // Bersihkan gelar untuk pencocokan nama dasar
            $clean_name = trim(preg_replace('/,?\s*(S\.H\.I\.|M\.H\.|S\.H\.|S\.Ag\.|Drs\.|Dra\.)/i', '', $pp_name));
            $user_pp = $this->CI->db->select('id')
                                    ->from('users')
                                    ->where('role', 'pp')
                                    ->group_start()
                                        ->like('nama', $clean_name)
                                        ->or_like('nama', strtok($clean_name, " "))
                                    ->group_end()
                                    ->get()->row();
            if ($user_pp) {
                return $user_pp->id;
            }
        }

        // Fallback: ambil user PP pertama yang aktif
        $first_pp = $this->CI->db->get_where('users', ['role' => 'pp', 'is_active' => 1])->row();
        if ($first_pp) {
            return $first_pp->id;
        }

        // Default ke user ID 2 (PP default)
        return 2;
    }

    /**
     * Membersihkan string HTML majelis hakim menjadi string rata/ringkas
     */
    private function clean_majelis_text($raw) {
        if (empty($raw)) return '';
        $text = str_replace(['</br>', '<br>', '<br/>'], '; ', $raw);
        $text = strip_tags($text);
        return trim($text);
    }

    /**
     * Memproses penunjukan mediator dari API
     */
    private function process_mediator_assignment($perkara_id, $item, $assigned_by_pp_id) {
        $nama_mediator   = trim($item['nama_mediator']);
        $status_mediator = isset($item['status_mediator']) ? strtoupper(trim($item['status_mediator'])) : 'N';
        $jenis_mediator  = ($status_mediator === 'H') ? 'hakim' : 'non_hakim';

        // Extract id_mediator / mediator_id dari API
        $id_mediator_api = !empty($item['id_mediator']) ? trim($item['id_mediator']) : (!empty($item['mediator_id']) ? trim($item['mediator_id']) : null);

        $mediator = null;
        if (!empty($id_mediator_api)) {
            // 1. Match berdasarkan id_mediator dari API
            $mediator = $this->CI->db->get_where('mediators', ['id_mediator' => $id_mediator_api])->row();
            // 2. Match berdasarkan ID lokal jika id_mediator_api numerik
            if (!$mediator && is_numeric($id_mediator_api)) {
                $mediator = $this->CI->db->get_where('mediators', ['id' => (int)$id_mediator_api])->row();
            }
        }
        // 3. Fallback match berdasarkan nama
        if (!$mediator) {
            $mediator = $this->CI->db->get_where('mediators', ['nama' => $nama_mediator])->row();
        }

        if (!$mediator) {
            $this->CI->db->insert('mediators', [
                'nama'          => $nama_mediator,
                'jenis'         => $jenis_mediator,
                'id_mediator'   => $id_mediator_api,
                'no_sertifikat' => 'SERTIF/API/' . date('Y'),
                'is_active'     => 1
            ]);
            $mediator_id = $this->CI->db->insert_id();
        } else {
            $mediator_id = $mediator->id;
            if (!empty($id_mediator_api) && empty($mediator->id_mediator)) {
                $this->CI->db->where('id', $mediator_id)->update('mediators', ['id_mediator' => $id_mediator_api]);
            }
        }

        // Check penunjukan perkara_mediator
        $pm = $this->CI->db->get_where('perkara_mediator', ['perkara_id' => $perkara_id])->row();
        if (!$pm) {
            $tgl_assign = !empty($item['tanggal_penetapan_mediator']) ? date('Y-m-d', strtotime($item['tanggal_penetapan_mediator'])) : date('Y-m-d');
            $this->CI->db->insert('perkara_mediator', [
                'perkara_id'      => $perkara_id,
                'mediator_id'     => $mediator_id,
                'tgl_penetapan'   => $tgl_assign,
                'status_mediator' => $status_mediator,
                'is_active'       => 1
            ]);

            // Record log riwayat penugasan jika tabel log ada
            if ($this->CI->db->table_exists('perkara_mediator_log')) {
                $this->CI->db->insert('perkara_mediator_log', [
                    'perkara_id'  => $perkara_id,
                    'mediator_id' => $mediator_id,
                    'assigned_by' => $assigned_by_pp_id,
                    'tgl_assign'  => date('Y-m-d H:i:s', strtotime($tgl_assign)),
                ]);
            }

            // Update status perkara ke 'proses'
            $this->CI->db->where('id', $perkara_id)->update('perkara', ['status' => 'proses']);

            // Kirim notifikasi penugasan ke Mediator (WA/Email jika memiliki no_hp/email)
            $this->trigger_assignment_notification($perkara_id, $mediator_id);
        } else if ($pm->mediator_id != $mediator_id) {
            // Jika terjadi perubahan penunjukan mediator
            $old_mediator_id = $pm->mediator_id;
            $this->CI->db->where('id', $pm->id)->update('perkara_mediator', [
                'mediator_id'     => $mediator_id,
                'status_mediator' => $status_mediator,
                'updated_at'      => date('Y-m-d H:i:s')
            ]);

            // Record log penggantian mediator
            if ($this->CI->db->table_exists('perkara_mediator_log')) {
                $this->CI->db->insert('perkara_mediator_log', [
                    'perkara_id'  => $perkara_id,
                    'mediator_id' => $mediator_id,
                    'assigned_by' => $assigned_by_pp_id,
                    'tgl_assign'  => date('Y-m-d H:i:s'),
                ]);
            }

            // Kirim notifikasi ke mediator baru & mediator lama
            $this->trigger_assignment_notification($perkara_id, $mediator_id, $old_mediator_id);
        }
    }

    /**
     * Helper untuk memicu notifikasi penugasan mediator via WA dan Email jika mediator memiliki no_hp atau email
     */
    private function trigger_assignment_notification($perkara_id, $mediator_id, $old_mediator_id = null) {
        if (!isset($this->CI->wagateway)) {
            $this->CI->load->library('WaGateway');
        }
        if (!isset($this->CI->emailgateway)) {
            $this->CI->load->library('EmailGateway');
        }

        // Kirim notifikasi penugasan ke mediator baru
        if (!empty($mediator_id)) {
            $this->CI->wagateway->kirim_penugasan_mediator($perkara_id, $mediator_id);
            $this->CI->emailgateway->kirim_penugasan_mediator($perkara_id, $mediator_id);
        }

        // Kirim notifikasi penggantian ke mediator lama (jika ada)
        if (!empty($old_mediator_id)) {
            $this->CI->wagateway->kirim_penggantian_mediator($perkara_id, $old_mediator_id);
            $this->CI->emailgateway->kirim_penggantian_mediator($perkara_id, $old_mediator_id);
        }
    }

    /**
     * Memproses parsing string pihak dan kuasa dari API SIPP secara Smart Upsert (Tanpa Delete)
     */
    private function process_pihak_parsing($perkara_id, $item) {
        $pihak_penggugat_id = null;
        $pihak_tergugat_id  = null;

        // 1. Parsing Penggugat (Upsert)
        if (!empty($item['penggugat'])) {
            $parsed_p = $this->parse_pihak_detail($item['penggugat']);
            $pihak_penggugat_id = $this->upsert_pihak($perkara_id, 'penggugat', $parsed_p);
        }

        // 2. Parsing Tergugat (Upsert)
        if (!empty($item['tergugat'])) {
            $parsed_t = $this->parse_pihak_detail($item['tergugat']);
            $pihak_tergugat_id = $this->upsert_pihak($perkara_id, 'tergugat', $parsed_t);
        }

        // 3. Parsing Kuasa Hukum Penggugat / Pemohon khusus dari API jika ada
        $kuasa_p_raw = !empty($item['kuasa_penggugat']) ? $item['kuasa_penggugat'] : (!empty($item['kuasa_pemohon']) ? $item['kuasa_pemohon'] : null);
        if ($kuasa_p_raw && $pihak_penggugat_id) {
            $this->insert_kuasa_from_raw($perkara_id, $pihak_penggugat_id, $kuasa_p_raw);
        }

        // 4. Parsing Kuasa Hukum Tergugat / Termohon khusus dari API jika ada
        $kuasa_t_raw = !empty($item['kuasa_tergugat']) ? $item['kuasa_tergugat'] : (!empty($item['kuasa_termohon']) ? $item['kuasa_termohon'] : null);
        if ($kuasa_t_raw && $pihak_tergugat_id) {
            $this->insert_kuasa_from_raw($perkara_id, $pihak_tergugat_id, $kuasa_t_raw);
        }

        // 5. Parsing General Kuasa Field ($item['kuasa']) jika khusus tidak ada
        if (!empty($item['kuasa']) && empty($kuasa_p_raw) && empty($kuasa_t_raw)) {
            $kuasa_items = $this->split_kuasa_names($item['kuasa']);
            foreach ($kuasa_items as $kuasa_raw) {
                if (empty(trim($kuasa_raw))) continue;

                $target_pihak_id = $pihak_penggugat_id;
                if (preg_match('/(?:tergugat|termohon)/i', $kuasa_raw) && $pihak_tergugat_id) {
                    $target_pihak_id = $pihak_tergugat_id;
                }

                $clean_kuasa = preg_replace('/^(?:Kuasa\s+)?(?:Penggugat|Pemohon|Tergugat|Termohon):\s*/i', '', $kuasa_raw);
                $parsed_k = $this->parse_pihak_detail($clean_kuasa);
                if (empty($parsed_k['nama']) || $parsed_k['nama'] === 'Tidak Diketahui') continue;

                $this->upsert_kuasa($perkara_id, $target_pihak_id, $parsed_k);
            }
        }
    }

    /**
     * Smart Upsert Pihak Perkara (Insert baru atau Update data eksisting tanpa menimpa Email/No. HP)
     */
    private function upsert_pihak($perkara_id, $jenis_pihak, $parsed_p) {
        if (empty($parsed_p['nama']) || $parsed_p['nama'] === 'Tidak Diketahui') return null;

        $existing = $this->CI->db->get_where('perkara_pihak', [
            'perkara_id'  => $perkara_id,
            'jenis_pihak' => $jenis_pihak,
            'nama'        => $parsed_p['nama']
        ])->row();

        if ($existing) {
            $update_data = [
                'nik'        => $parsed_p['nik'] ?: $existing->nik,
                'ttl'        => $parsed_p['ttl'] ?: $existing->ttl,
                'pekerjaan'  => $parsed_p['pekerjaan'] ?: $existing->pekerjaan,
                'pendidikan' => $parsed_p['pendidikan'] ?: $existing->pendidikan,
                'alamat'     => $parsed_p['alamat'] ?: $existing->alamat,
            ];
            if (empty($existing->email) && !empty($parsed_p['email'])) {
                $update_data['email'] = $parsed_p['email'];
            }
            if (empty($existing->no_hp) && !empty($parsed_p['no_hp'])) {
                $update_data['no_hp'] = $parsed_p['no_hp'];
            }
            $this->CI->db->where('id', $existing->id)->update('perkara_pihak', $update_data);
            return $existing->id;
        } else {
            $this->CI->db->insert('perkara_pihak', [
                'perkara_id'  => $perkara_id,
                'jenis_pihak' => $jenis_pihak,
                'nama'        => $parsed_p['nama'],
                'nik'         => $parsed_p['nik'],
                'ttl'         => $parsed_p['ttl'],
                'pekerjaan'   => $parsed_p['pekerjaan'],
                'pendidikan'  => $parsed_p['pendidikan'],
                'alamat'      => $parsed_p['alamat'],
                'email'       => $parsed_p['email'],
                'no_hp'       => $parsed_p['no_hp'],
                'urutan'      => 1
            ]);
            return $this->CI->db->insert_id();
        }
    }

    /**
     * Smart Upsert Kuasa Hukum (Insert baru atau Update data eksisting tanpa menimpa Email/No. HP)
     */
    private function upsert_kuasa($perkara_id, $pihak_id, $parsed_k) {
        if (empty($parsed_k['nama']) || $parsed_k['nama'] === 'Tidak Diketahui') return null;

        $existing = $this->CI->db->get_where('perkara_kuasa', [
            'perkara_id' => $perkara_id,
            'pihak_id'   => $pihak_id,
            'nama'       => $parsed_k['nama']
        ])->row();

        if ($existing) {
            $update_data = [
                'nik'       => $parsed_k['nik'] ?: $existing->nik,
                'ttl'       => $parsed_k['ttl'] ?: $existing->ttl,
                'pekerjaan' => $parsed_k['pekerjaan'] ?: $existing->pekerjaan,
                'alamat'    => $parsed_k['alamat'] ?: $existing->alamat,
            ];
            if (empty($existing->email) && !empty($parsed_k['email'])) {
                $update_data['email'] = $parsed_k['email'];
            }
            if (empty($existing->no_hp) && !empty($parsed_k['no_hp'])) {
                $update_data['no_hp'] = $parsed_k['no_hp'];
            }
            $this->CI->db->where('id', $existing->id)->update('perkara_kuasa', $update_data);
            return $existing->id;
        } else {
            $this->CI->db->insert('perkara_kuasa', [
                'perkara_id' => $perkara_id,
                'pihak_id'   => $pihak_id,
                'nama'       => $parsed_k['nama'],
                'nik'        => $parsed_k['nik'],
                'ttl'        => $parsed_k['ttl'],
                'pekerjaan'  => $parsed_k['pekerjaan'] ?: 'Pengacara',
                'alamat'     => $parsed_k['alamat'],
                'email'      => $parsed_k['email'],
                'no_hp'      => $parsed_k['no_hp']
            ]);
            return $this->CI->db->insert_id();
        }
    }

    /**
     * Helper untuk memecah string Kuasa Hukum yang berisi banyak pengacara dari SIPP API.
     * Mencegah pemecahan koma pada alamat atau detail di dalam kurung.
     */
    /**
     * Helper untuk memecah string Kuasa Hukum yang berisi banyak pengacara dari SIPP API.
     * Menggunakan pemisah resmi SIPP API (' | ', '|', ';', newline, atau prefix 'Nama:')
     */
    private function split_kuasa_names($raw) {
        if (empty($raw)) return [];

        $raw = trim($raw);

        // Jika string berisi kurung detail (NIK: ... # Alamat: ...) atau terdapat prefix "Nama:"
        if (preg_match('/Nama:\s*/i', $raw) || preg_match('/\(.*?(?:NIK|TTL|Alamat|Pekerjaan):.*?\)/i', $raw) || strpos($raw, '#') !== false) {
            // Split berdasarkan pemisah resmi antar advokat: ' | ', '|', ';', newline, atau jika ada 'Nama:' baru
            $blocks = preg_split('/(?:\s*\|\s*|\s*;\s*|[\r\n]+|(?=\bNama:\s*))/i', $raw);
            $results = [];
            foreach ($blocks as $b) {
                $b = trim($b);
                if (empty($b)) continue;
                // Pastikan kurung penutup dipulihkan jika terpotong
                if (strpos($b, '(') !== false && strpos($b, ')') === false) {
                    $b .= ')';
                }
                $results[] = $b;
            }
            return array_filter($results);
        }

        // Jika string polos (tanpa kurung NIK/#)
        $raw_clean = str_replace(['|', ';', "\r\n", "\n"], ',', $raw);
        $tokens = preg_split('/,\s*(?=[A-Z0-9\.\s]{3,})/i', $raw_clean);
        $names = [];
        $current_name = '';
        foreach ($tokens as $token) {
            $token = trim($token);
            if (empty($token)) continue;
            if (empty($current_name)) {
                $current_name = $token;
            } else {
                if (preg_match('/^(?:S\.?H\.?|M\.?H\.?|S\.?Ag\.?|S\.?H\.?I\.?|M\.?Ag\.?|M\.?H\.I\.?|S\.?P\.?d\.?|S\.?E\.?|C\.?L\.?A\.?|C\.?T\.?C\.?|C\.?P\.?L\.?|C\.?P\.?A\.?r\.?b\.?)$/i', $token)) {
                    $current_name .= ', ' . $token;
                } else {
                    $names[] = trim(preg_replace('/^(?:Kuasa\s+)?(?:Penggugat|Pemohon|Tergugat|Termohon):\s*/i', '', $current_name));
                    $current_name = $token;
                }
            }
        }
        if (!empty($current_name)) {
            $names[] = trim(preg_replace('/^(?:Kuasa\s+)?(?:Penggugat|Pemohon|Tergugat|Termohon):\s*/i', '', $current_name));
        }
        return array_filter($names);
    }

    /**
     * Helper untuk insert kuasa dari raw string secara Smart Upsert
     */
    private function insert_kuasa_from_raw($perkara_id, $pihak_id, $raw) {
        $kuasa_items = $this->split_kuasa_names($raw);
        foreach ($kuasa_items as $k_item) {
            if (empty(trim($k_item))) continue;
            $parsed_k = $this->parse_pihak_detail($k_item);
            if (empty($parsed_k['nama']) || $parsed_k['nama'] === 'Tidak Diketahui') continue;

            $this->upsert_kuasa($perkara_id, $pihak_id, $parsed_k);
        }
    }

    /**
     * Helper Parsing Rinci String Format:
     * "Nama Kuasa, S.H. (NIK: YYY# TTL: ZZZ# Pekerjaan: AAA# Pendidikan: BBB# Alamat: CCC# Email: DDD# No HP: EEE)"
     */
    private function parse_pihak_detail($raw) {
        $data = [
            'nama'       => 'Tidak Diketahui',
            'nik'        => null,
            'ttl'        => null,
            'pekerjaan'  => null,
            'pendidikan' => null,
            'alamat'     => null,
            'email'      => null,
            'no_hp'      => null
        ];

        if (empty($raw)) return $data;

        // 1. Extract Nama (Ambil teks SEBELUM kurung detail '(' atau 'NIK:')
        $clean_nama = preg_replace('/\s*\(.*$/s', '', $raw);
        $clean_nama = preg_replace('/^(?:Nama:\s*|(?:Kuasa\s+)?(?:Penggugat|Pemohon|Tergugat|Termohon):\s*)/i', '', $clean_nama);
        $clean_nama = trim($clean_nama, " \t\n\r\0\x0B,;");

        // Filter jika nama mengandung kata alamat/detail dan bukan nama orang
        if (empty($clean_nama) || preg_match('/^(?:Kecamatan|Kelurahan|Kabupaten|Desa|Kota|Provinsi|JL\.|NIK:|TTL:|Alamat:)/i', $clean_nama)) {
            return $data;
        }

        $data['nama'] = $clean_nama;

        // 2. Extract NIK
        if (preg_match('/NIK:\s*([^#\)]+)/i', $raw, $m)) {
            $val = trim($m[1]);
            if ($val !== '-' && !empty($val)) $data['nik'] = $val;
        }

        // 3. Extract TTL
        if (preg_match('/TTL:\s*([^#\)]+)/i', $raw, $m)) {
            $val = trim($m[1]);
            if ($val !== '-' && $val !== '00-00-0000' && !empty($val)) $data['ttl'] = $val;
        }

        // 4. Extract Pekerjaan
        if (preg_match('/Pekerjaan:\s*([^#\)]+)/i', $raw, $m)) {
            $val = trim($m[1]);
            if ($val !== '-' && !empty($val)) $data['pekerjaan'] = $val;
        }

        // 5. Extract Pendidikan
        if (preg_match('/Pendidikan:\s*([^#\)]+)/i', $raw, $m)) {
            $val = trim($m[1]);
            if ($val !== '-' && !empty($val)) $data['pendidikan'] = $val;
        }

        // 6. Extract Alamat
        if (preg_match('/Alamat:\s*([^#\)]+)/i', $raw, $m)) {
            $val = trim($m[1]);
            if ($val !== '-' && !empty($val)) $data['alamat'] = $val;
        }

        // 7. Extract Email
        if (preg_match('/Email:\s*([^#\)\s]+)/i', $raw, $m)) {
            $val = trim($m[1]);
            if ($val !== '-' && !empty($val) && filter_var($val, FILTER_VALIDATE_EMAIL)) {
                $data['email'] = $val;
            }
        }

        // 8. Extract No HP
        if (preg_match('/(?:No\s*HP|HP|Telepon|WA):\s*([^#\)]+)/i', $raw, $m)) {
            $val = trim($m[1]);
            if ($val !== '-' && !empty($val)) $data['no_hp'] = $val;
        }

        return $data;
    }
}

