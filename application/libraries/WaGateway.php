<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * WaGateway — Library Notifikasi WhatsApp (Fonnte / Generic API)
 */
class WaGateway {

    private $CI;

    public function __construct() {
        $this->CI =& get_instance();
    }

    /**
     * Cek apakah notifikasi WA diaktifkan di Pengaturan Aplikasi
     */
    public function is_enabled() {
        return get_app_setting('wa_notif_active', '0') === '1';
    }

    /**
     * Kirim pesan WA ke nomor HP tertentu.
     */
    public function kirim($no_hp, $pesan, $perkara_id = null) {
        if (!$this->is_enabled() || empty($no_hp)) return false;

        $token   = get_app_setting('wa_api_token', '');
        $api_url = get_app_setting('wa_api_url', 'https://api.fonnte.com/send');

        if (empty($token) || empty($api_url)) {
            $this->_log($perkara_id, 'wa', $no_hp, 'Notifikasi WA', $pesan, 'gagal', 'API Token WA belum diisi di Pengaturan Aplikasi.');
            return false;
        }

        // Normalisasi nomor HP (08xx -> 628xx)
        $no = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $no_hp));

        try {
            $ch = curl_init($api_url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => [
                    'target'  => $no,
                    'message' => $pesan,
                ],
                CURLOPT_HTTPHEADER     => [
                    'Authorization: ' . $token,
                ],
                CURLOPT_TIMEOUT        => 10,
            ]);

            $response = curl_exec($ch);
            $err_no   = curl_errno($ch);
            $err_str  = curl_error($ch);
            curl_close($ch);

            if ($err_no) {
                $this->_log($perkara_id, 'wa', $no, 'Notifikasi WA', $pesan, 'gagal', "cURL Error ({$err_no}): {$err_str}");
                return false;
            }

            $res = json_decode($response, true);
            if (isset($res['status']) && $res['status'] === true) {
                $this->_log($perkara_id, 'wa', $no, 'Notifikasi WA', $pesan, 'terkirim', null);
                return true;
            } else {
                $msg = $res['reason'] ?? $res['message'] ?? 'Respon API menunjukkan kegagalan.';
                $this->_log($perkara_id, 'wa', $no, 'Notifikasi WA', $pesan, 'gagal', $msg);
                return false;
            }
        } catch (\Throwable $e) {
            $this->_log($perkara_id, 'wa', $no, 'Notifikasi WA', $pesan, 'gagal', $e->getMessage());
            return false;
        }
    }

    private function _log($perkara_id, $jenis, $penerima, $subjek, $pesan, $status, $error = null) {
        $this->CI->db->insert('log_notifikasi', [
            'perkara_id'    => $perkara_id,
            'jenis'         => $jenis,
            'penerima'      => $penerima,
            'subjek'        => $subjek,
            'pesan'         => $pesan,
            'status'        => $status,
            'error_message' => $error,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Helper broadcast notifikasi jadwal mediasi ke seluruh pihak perkara.
     */
    public function kirim_jadwal($perkara_id, $tgl, $jam_mulai, $jam_selesai, $ruangan_id, $link_virtual = null, $platform_virtual = null, $type = 'baru', $keterangan = null) {
        if (!$this->is_enabled()) return;

        // Backward compatibility boolean parameter conversion
        if ($type === true)  $type = 'edit';
        if ($type === false) $type = 'baru';

        $this->CI->load->model(['M_perkara', 'M_ruangan']);
        $pihak   = $this->CI->M_perkara->get_pihak($perkara_id);
        $perkara = $this->CI->M_perkara->get_by_id($perkara_id);

        if (!$perkara) return;

        $ruangan = $ruangan_id ? $this->CI->M_ruangan->get_by_id($ruangan_id) : null;
        $satker  = get_app_setting('nama_satker', 'Pengadilan Agama Gorontalo');

        // Tentukan label tempat
        if ($link_virtual) {
            $platform_label = $platform_virtual ?: 'Virtual Meeting';
            $tempat = "🎥 {$platform_label} (Online)";
        } elseif ($ruangan) {
            $tempat = $ruangan->nama_ruangan . ' (' . $satker . ')';
        } else {
            $tempat = 'Tempat lain / Online (Konfirmasi ke Mediator)';
        }

        $tgl_indo_str = tgl_indo($tgl, true);
        $tracking_url = site_url('publik/cari?nomor_perkara=' . urlencode($perkara->nomor_perkara));

        $virtual_line = $link_virtual
            ? "\n\u{1F517} *Link Gabung Meeting:*\n{$link_virtual}"
            : '';

        $catatan_line = $keterangan
            ? "\n• Catatan Mediator: _\"{$keterangan}\"_"
            : '';

        if ($type === 'reschedule') {
            $title_wa = "📅 *PENJADWALAN ULANG MEDIASI " . strtoupper($satker) . "*";
            $intro_wa = "Diberitahukan bahwa sesi mediasi perkara Anda telah *DIJADWAL ULANG* ke tanggal/waktu berikut:";
        } elseif ($type === 'edit') {
            $title_wa = "⚠️ *PERUBAHAN DETAIL JADWAL " . strtoupper($satker) . "*";
            $intro_wa = "Diberitahukan bahwa terdapat *PERUBAHAN DETAIL JADWAL* (jam/ruangan/link meeting) untuk sesi mediasi perkara Anda:";
        } else {
            $title_wa = "📢 *PANGGILAN MEDIASI " . strtoupper($satker) . "*";
            $intro_wa = "Diberitahukan jadwal sesi mediasi untuk perkara Anda:";
        }

        $pesan = "{$title_wa}\n\n" .
                 "Yth. Bapak/Ibu Pihak Berperkara,\n" .
                 "{$intro_wa}\n\n" .
                 "• Nomor Perkara : *{$perkara->nomor_perkara}*\n" .
                 "• Jenis Perkara : {$perkara->jenis_perkara}\n" .
                 "• Tanggal       : *{$tgl_indo_str}*\n" .
                 "• Waktu         : *" . substr($jam_mulai,0,5) . " – " . substr($jam_selesai,0,5) . " WITA*\n" .
                 "• Tempat        : *{$tempat}*\n" .
                 "• Mediator      : {$perkara->nama_mediator}" .
                 $catatan_line .
                 $virtual_line . "\n\n" .
                 "🔗 *Pantau Jadwal Mediasi Online:*\n{$tracking_url}\n\n" .
                 "Harap hadir tepat waktu dengan membawa Kartu Identitas Diri (KTP/SIM/Paspor).\n\n" .
                 "Terima Kasih.\n---\n*{$satker}*";

        foreach ($pihak as $p) {
            if (!empty($p->no_hp)) {
                $this->kirim($p->no_hp, $pesan, $perkara_id);
            }
        }
    }

    /**
     * Broadcast notifikasi pembatalan sesi mediasi ke WhatsApp para pihak.
     */
    public function kirim_batal($sesi_id, $alasan = 'Dibatalkan oleh mediator.') {
        if (!$this->is_enabled()) return;

        $this->CI->load->model(['M_jadwal', 'M_perkara']);
        $sesi = $this->CI->M_jadwal->get_by_id($sesi_id);
        if (!$sesi) return;

        $perkara = $this->CI->M_perkara->get_by_id($sesi->perkara_id);
        $pihak   = $this->CI->M_perkara->get_pihak($sesi->perkara_id);
        if (!$perkara) return;

        $satker  = get_app_setting('nama_satker', 'Pengadilan Agama Gorontalo');
        $tgl_indo_str = tgl_indo($sesi->tgl_mediasi, true);
        $tracking_url = site_url('publik/cari?nomor_perkara=' . urlencode($perkara->nomor_perkara));

        $pesan = "❌ *PEMBATALAN SESI MEDIASI " . strtoupper($satker) . "*\n\n" .
                 "Yth. Bapak/Ibu Pihak Berperkara,\n" .
                 "Diberitahukan bahwa pelaksanaan sesi mediasi perkara Anda berikut ini telah *DIBATALKAN*:\n\n" .
                 "• Nomor Perkara : *{$perkara->nomor_perkara}*\n" .
                 "• Tanggal Sesi  : ~{$tgl_indo_str}~\n" .
                 "• Alasan        : _\"{$alasan}\"_\n" .
                 "• Mediator      : {$perkara->nama_mediator}\n\n" .
                 "🔗 *Pantau Status Mediasi Online:*\n{$tracking_url}\n\n" .
                 "Terima Kasih.\n---\n*{$satker}*";

        foreach ($pihak as $p) {
            if (!empty($p->no_hp)) {
                $this->kirim($p->no_hp, $pesan, $sesi->perkara_id);
            }
        }
    }
}
