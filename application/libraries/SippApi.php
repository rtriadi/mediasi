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
    public function sync($params = []) {
        $api_res = $this->fetch_data($params);

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
                $tgl_penetapan = !empty($item['tanggal_penetapan_mediator']) ? date('Y-m-d', strtotime($item['tanggal_penetapan_mediator'])) : date('Y-m-d');
                $tgl_batas     = date('Y-m-d', strtotime($tgl_penetapan . " +{$batas_hari} days"));

                // Pengecekan Perkara Eksisting
                $existing = $this->CI->db->get_where('perkara', ['nomor_perkara' => $nomor_perkara])->row();

                $data_perkara = [
                    'nomor_perkara'              => $nomor_perkara,
                    'perkara_id_sipp'            => isset($item['perkara_id']) ? $item['perkara_id'] : null,
                    'jenis_perkara_id'           => $jenis_perkara_id,
                    'nama_hakim'                 => !empty($clean_hakim) ? $clean_hakim : 'Majelis Hakim',
                    'majelis_hakim'              => $majelis_raw,
                    'majelis_id'                 => isset($item['majelis_id']) ? $item['majelis_id'] : null,
                    'panitera_pengganti_id_sipp' => isset($item['panitera_pengganti_id']) ? $item['panitera_pengganti_id'] : null,
                    'panitera_sidang'            => isset($item['panitera_sidang']) ? $item['panitera_sidang'] : null,
                    'tgl_penetapan_mediator'     => $tgl_penetapan,
                    'tgl_batas_mediasi'          => $tgl_batas,
                    'pp_id'                      => $pp_id,
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

                // 6. Handling Parsing Pihak (Penggugat & Tergugat & Kuasa)
                $this->process_pihak_parsing($perkara_id, $item);

                $details[] = "Perkara {$nomor_perkara} berhasil diproses.";

            } catch (Exception $e) {
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

        // Cari atau buat mediator
        $mediator = $this->CI->db->get_where('mediators', ['nama' => $nama_mediator])->row();
        if (!$mediator) {
            $this->CI->db->insert('mediators', [
                'nama'          => $nama_mediator,
                'jenis'         => $jenis_mediator,
                'no_sertifikat' => 'SERTIF/API/' . date('Y'),
                'is_active'     => 1
            ]);
            $mediator_id = $this->CI->db->insert_id();
        } else {
            $mediator_id = $mediator->id;
        }

        // Check penunjukan perkara_mediator
        $pm = $this->CI->db->get_where('perkara_mediator', ['perkara_id' => $perkara_id])->row();
        if (!$pm) {
            $this->CI->db->insert('perkara_mediator', [
                'perkara_id'  => $perkara_id,
                'mediator_id' => $mediator_id,
                'assigned_by' => $assigned_by_pp_id,
                'tgl_assign'  => !empty($item['tanggal_penetapan_mediator']) ? date('Y-m-d H:i:s', strtotime($item['tanggal_penetapan_mediator'])) : date('Y-m-d H:i:s')
            ]);

            // Update status perkara ke 'proses'
            $this->CI->db->where('id', $perkara_id)->update('perkara', ['status' => 'proses']);
        }
    }

    /**
     * Memproses parsing string pihak dari API SIPP
     */
    private function process_pihak_parsing($perkara_id, $item) {
        // Hapus data pihak lama saat re-sync
        $this->CI->db->where('perkara_id', $perkara_id)->delete('perkara_pihak');

        $kuasa_hukum_str = '';
        if (!empty($item['kuasa'])) {
            $kuasa_hukum_str = $this->parse_kuasa_names($item['kuasa']);
        }

        // Parsing Penggugat
        if (!empty($item['penggugat'])) {
            $parsed_p = $this->parse_single_pihak_string($item['penggugat']);
            $this->CI->db->insert('perkara_pihak', [
                'perkara_id'  => $perkara_id,
                'jenis'       => 'penggugat',
                'nama'        => $parsed_p['nama'],
                'kuasa_hukum' => $kuasa_hukum_str,
                'no_hp'       => $parsed_p['no_hp'],
                'email'       => $parsed_p['email'],
                'urutan'      => 1
            ]);
        }

        // Parsing Tergugat
        if (!empty($item['tergugat'])) {
            $parsed_t = $this->parse_single_pihak_string($item['tergugat']);
            $this->CI->db->insert('perkara_pihak', [
                'perkara_id'  => $perkara_id,
                'jenis'       => 'tergugat',
                'nama'        => $parsed_t['nama'],
                'kuasa_hukum' => null,
                'no_hp'       => $parsed_t['no_hp'],
                'email'       => $parsed_t['email'],
                'urutan'      => 1
            ]);
        }
    }

    /**
     * Parsing string format: "Nama: XXX (NIK: YYY# TTL: ZZZ# Pekerjaan: AAA# Alamat: BBB# Email: CCC)"
     */
    private function parse_single_pihak_string($raw) {
        $nama  = 'Tidak Diketahui';
        $no_hp = null;
        $email = null;

        if (preg_match('/Nama:\s*([^(#]+)/i', $raw, $matches)) {
            $nama = trim($matches[1]);
        } else {
            $nama = trim(strip_tags($raw));
        }

        if (preg_match('/Email:\s*([^)#\s]+)/i', $raw, $matches)) {
            $candidate = trim($matches[1]);
            if (!empty($candidate) && $candidate !== '-') {
                $email = $candidate;
            }
        }

        if (preg_match('/No HP:\s*([^)#\s]+)/i', $raw, $matches)) {
            $candidate = trim($matches[1]);
            if (!empty($candidate) && $candidate !== '-') {
                $no_hp = $candidate;
            }
        }

        return [
            'nama'  => $nama,
            'email' => $email,
            'no_hp' => $no_hp
        ];
    }

    /**
     * Extract daftar nama kuasa dari string terpisah '|'
     */
    private function parse_kuasa_names($raw) {
        $parts = explode('|', $raw);
        $names = [];
        foreach ($parts as $part) {
            if (preg_match('/Nama:\s*([^(#]+)/i', $part, $m)) {
                $names[] = trim($m[1]);
            }
        }
        return !empty($names) ? implode(', ', $names) : trim(strip_tags($raw));
    }
}
