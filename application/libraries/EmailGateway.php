<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * EmailGateway — Library Notifikasi Email (SMTP)
 */
class EmailGateway {

    private $CI;

    public function __construct() {
        $this->CI =& get_instance();
    }

    /**
     * Cek apakah notifikasi Email diaktifkan di Pengaturan Aplikasi
     */
    public function is_enabled() {
        return get_app_setting('email_notif_active', '1') === '1';
    }

    /**
     * Kirim email HTML via SMTP.
     */
    public function kirim($to_email, $subject, $message_body, $perkara_id = null) {
        if (!$this->is_enabled() || empty($to_email)) return false;

        $smtp_user = get_app_setting('smtp_user', '');
        $smtp_host = get_app_setting('smtp_host', '');

        // Jika SMTP user atau host belum diset, bypass pengiriman dan log status
        if (empty($smtp_host) || empty($smtp_user)) {
            $this->_log($perkara_id, 'email', $to_email, $subject, $message_body, 'gagal', 'Pengaturan SMTP (host/user) belum diisi di Pengaturan Aplikasi.');
            return false;
        }

        try {
            $config = [
                'protocol'    => 'smtp',
                'smtp_host'   => $smtp_host,
                'smtp_port'   => (int)get_app_setting('smtp_port', '587'),
                'smtp_user'   => $smtp_user,
                'smtp_pass'   => get_app_setting('smtp_pass', ''),
                'smtp_crypto' => get_app_setting('smtp_crypto', 'tls'),
                'mailtype'    => 'html',
                'charset'     => 'utf-8',
                'newline'     => "\r\n",
                'crlf'        => "\r\n",
            ];

            $this->CI->load->library('email');
            $this->CI->email->initialize($config);

            $from_name = get_app_setting('mail_from_name', get_app_setting('nama_aplikasi', 'SIPO-MEDIASI'));
            $this->CI->email->from($smtp_user, $from_name);
            $this->CI->email->to($to_email);
            $this->CI->email->subject($subject);
            $this->CI->email->message($message_body);

            $sent = @$this->CI->email->send();
            if ($sent) {
                $this->_log($perkara_id, 'email', $to_email, $subject, $message_body, 'terkirim', null);
                return true;
            } else {
                $err = strip_tags($this->CI->email->print_debugger(['headers'])) ?: 'Gagal terhubung ke SMTP server atau ditolak oleh provider.';
                $this->_log($perkara_id, 'email', $to_email, $subject, $message_body, 'gagal', $err);
                return false;
            }
        } catch (\Throwable $e) {
            $this->_log($perkara_id, 'email', $to_email, $subject, $message_body, 'gagal', $e->getMessage());
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
     * Broadcast undangan/panggilan sesi mediasi ke email para pihak berperkara.
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
        $app_name= get_app_setting('nama_aplikasi', 'SIPO-MEDIASI');

        if ($link_virtual) {
            $platform_label = $platform_virtual ?: 'Virtual Meeting';
            $tempat = "🎥 {$platform_label} (Online)";
            $tempat_detail = "<a href='{$link_virtual}' style='color: #2563eb; font-weight: bold;'>{$link_virtual}</a>";
        } elseif ($ruangan) {
            $tempat = $ruangan->nama_ruangan;
            $tempat_detail = htmlspecialchars($ruangan->nama_ruangan) . " ({$satker})";
        } else {
            $tempat = 'Tempat lain';
            $tempat_detail = 'Konfirmasi ke Mediator';
        }

        $tgl_indo_str = tgl_indo($tgl, true);
        $tracking_url = site_url('publik/cari?nomor_perkara=' . urlencode($perkara->nomor_perkara));

        if ($type === 'reschedule') {
            $subject = "[PENJADWALAN ULANG] Mediasi Perkara No. {$perkara->nomor_perkara} - {$satker}";
            $banner_title = "📅 PENJADWALAN ULANG MEDIASI";
            $intro_text = "Diberitahukan bahwa sesi mediasi perkara Anda telah <strong>DIJADWAL ULANG</strong> ke waktu berikut:";
        } elseif ($type === 'edit') {
            $subject = "[PERUBAHAN JADWAL] Mediasi Perkara No. {$perkara->nomor_perkara} - {$satker}";
            $banner_title = "⚠️ PERUBAHAN DETAIL JADWAL MEDIASI";
            $intro_text = "Diberitahukan bahwa terdapat <strong>PERUBAHAN DETAIL JADWAL</strong> (jam/ruangan/link meeting) untuk sesi mediasi perkara Anda:";
        } else {
            $subject = "[PANGGILAN MEDIASI] Perkara No. {$perkara->nomor_perkara} - {$satker}";
            $banner_title = "📢 PANGGILAN MEDIASI";
            $intro_text = "Diberitahukan jadwal sesi mediasi untuk perkara Anda:";
        }

        $safe_ket = htmlspecialchars($keterangan ?? '');
        $ket_html = $keterangan ? "<tr><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; color: #64748b;'>Catatan Mediator</td><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; font-style: italic;'>&ldquo;{$safe_ket}&rdquo;</td></tr>" : "";

        $html_body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px;'>
            <div style='background-color: #1e3a8a; color: #ffffff; padding: 15px 20px; border-radius: 8px 8px 0 0;'>
                <h2 style='margin: 0; font-size: 18px;'>{$banner_title}</h2>
                <p style='margin: 5px 0 0 0; font-size: 12px; color: #93c5fd;'>{$satker} — {$app_name}</p>
            </div>
            <div style='padding: 20px; background-color: #ffffff;'>
                <p>Yth. Bapak/Ibu Pihak Berperkara,</p>
                <p>{$intro_text}</p>
                <table style='width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 13px;'>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 140px;'>Nomor Perkara</td><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; font-weight: bold;'>{$perkara->nomor_perkara}</td></tr>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; color: #64748b;'>Jenis Perkara</td><td style='padding: 8px; border-bottom: 1px solid #f1f5f9;'>{$perkara->jenis_perkara}</td></tr>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; color: #64748b;'>Tanggal Sesi</td><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; font-weight: bold; color: #1e3a8a;'>{$tgl_indo_str}</td></tr>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; color: #64748b;'>Waktu</td><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; font-weight: bold;'>" . substr($jam_mulai,0,5) . " – " . substr($jam_selesai,0,5) . " WITA</td></tr>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; color: #64748b;'>Tempat / Lokasi</td><td style='padding: 8px; border-bottom: 1px solid #f1f5f9;'>{$tempat_detail}</td></tr>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; color: #64748b;'>Mediator</td><td style='padding: 8px; border-bottom: 1px solid #f1f5f9;'>{$perkara->nama_mediator}</td></tr>
                    {$ket_html}
                </table>

                <div style='margin: 25px 0 15px 0; text-align: center;'>
                    <a href='{$tracking_url}' style='display: inline-block; background-color: #2563eb; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-weight: bold; font-size: 13px;'>
                        🔍 Pantau Jadwal Mediasi Online
                    </a>
                </div>
                <p style='font-size: 12px; color: #64748b; font-style: italic; text-align: center; margin-top: 15px;'>Harap hadir tepat waktu dengan membawa Kartu Identitas Diri (KTP/SIM/Paspor).</p>

                <hr style='border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;' />
                <p style='font-size: 11px; color: #94a3b8; margin: 0;'>Pesan ini dikirimkan secara otomatis oleh Sistem Informasi Mediasi {$satker}.</p>
            </div>
        </div>
        ";

        foreach ($pihak as $p) {
            $email = trim($p->email ?? '');
            if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->kirim($email, $subject, $html_body, $perkara_id);
            }
        }
    }

    /**
     * Broadcast notifikasi pembatalan sesi mediasi ke email para pihak.
     */
    public function kirim_batal($sesi_id, $alasan = 'Dibatalkan oleh mediator.') {
        if (!$this->is_enabled()) return;

        $this->CI->load->model(['M_jadwal', 'M_perkara', 'M_ruangan']);
        $sesi = $this->CI->M_jadwal->get_by_id($sesi_id);
        if (!$sesi) return;

        $perkara = $this->CI->M_perkara->get_by_id($sesi->perkara_id);
        $pihak   = $this->CI->M_perkara->get_pihak($sesi->perkara_id);
        if (!$perkara) return;

        $satker  = get_app_setting('nama_satker', 'Pengadilan Agama Gorontalo');
        $app_name= get_app_setting('nama_aplikasi', 'SIPO-MEDIASI');
        $tgl_indo_str = tgl_indo($sesi->tgl_mediasi, true);
        $tracking_url = site_url('publik/cari?nomor_perkara=' . urlencode($perkara->nomor_perkara));

        $subject = "[PEMBATALAN SESI MEDIASI] Perkara No. {$perkara->nomor_perkara} - {$satker}";

        $safe_alasan = htmlspecialchars($alasan);
        $html_body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px;'>
            <div style='background-color: #dc2626; color: #ffffff; padding: 15px 20px; border-radius: 8px 8px 0 0;'>
                <h2 style='margin: 0; font-size: 18px;'>❌ PEMBATALAN SESI MEDIASI</h2>
                <p style='margin: 5px 0 0 0; font-size: 12px; color: #fef2f2;'>{$satker} — {$app_name}</p>
            </div>
            <div style='padding: 20px; background-color: #ffffff;'>
                <p>Yth. Bapak/Ibu Pihak Berperkara,</p>
                <p>Diberitahukan bahwa pelaksanaan sesi mediasi berikut telah <strong>DIBATALKAN</strong>:</p>
                <table style='width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 13px;'>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 140px;'>Nomor Perkara</td><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; font-weight: bold;'>{$perkara->nomor_perkara}</td></tr>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; color: #64748b;'>Tanggal Sesi</td><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; font-weight: bold; text-decoration: line-through; color: #dc2626;'>{$tgl_indo_str}</td></tr>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; color: #64748b;'>Alasan Pembatalan</td><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; font-style: italic; color: #b91c1c;'>&ldquo;{$safe_alasan}&rdquo;</td></tr>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; color: #64748b;'>Mediator</td><td style='padding: 8px; border-bottom: 1px solid #f1f5f9;'>{$perkara->nama_mediator}</td></tr>
                </table>

                <div style='margin: 25px 0 15px 0; text-align: center;'>
                    <a href='{$tracking_url}' style='display: inline-block; background-color: #2563eb; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-weight: bold; font-size: 13px;'>
                        🔍 Pantau Status Mediasi Online
                    </a>
                </div>

                <hr style='border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;' />
                <p style='font-size: 11px; color: #94a3b8; margin: 0;'>Pesan ini dikirimkan secara otomatis oleh Sistem Informasi Mediasi {$satker}.</p>
            </div>
        </div>
        ";

        foreach ($pihak as $p) {
            $email = trim($p->email ?? '');
            if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->kirim($email, $subject, $html_body, $sesi->perkara_id);
            }
        }
    }

    /**
     * Kirim notifikasi penugasan perkara ke email mediator.
     */
    public function kirim_penugasan_mediator($perkara_id, $mediator_id) {
        if (!$this->is_enabled()) return;

        $this->CI->load->model(['M_perkara', 'M_mediator']);
        $perkara  = $this->CI->M_perkara->get_by_id($perkara_id);
        $mediator = $this->CI->M_mediator->get_by_id($mediator_id);

        if (!$perkara || !$mediator) return;

        $email = trim($mediator->email ?? '');
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) return;

        $satker   = get_app_setting('nama_satker', 'Pengadilan Agama Gorontalo');
        $app_name = get_app_setting('nama_aplikasi', 'SIPO-MEDIASI');
        $tgl_batas = isset($perkara->tgl_batas_mediasi) ? tgl_indo($perkara->tgl_batas_mediasi, true) : '-';
        $login_url = site_url('auth/login');

        $subject = "[PENUGASAN MEDIASI] Perkara No. {$perkara->nomor_perkara} - {$satker}";

        $html_body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px;'>
            <div style='background-color: #0f766e; color: #ffffff; padding: 15px 20px; border-radius: 8px 8px 0 0;'>
                <h2 style='margin: 0; font-size: 18px;'>🧑‍⚖️ PENUGASAN MEDIASI</h2>
                <p style='margin: 5px 0 0 0; font-size: 12px; color: #99f6e4;'>{$satker} — {$app_name}</p>
            </div>
            <div style='padding: 20px; background-color: #ffffff;'>
                <p>Yth. <strong>" . htmlspecialchars($mediator->nama) . "</strong>,</p>
                <p>Anda telah <strong>DITUGASKAN</strong> sebagai Mediator untuk perkara berikut. Harap segera membuat jadwal sesi mediasi pertama.</p>
                <table style='width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 13px;'>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 160px;'>Nomor Perkara</td><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; font-weight: bold;'>{$perkara->nomor_perkara}</td></tr>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; color: #64748b;'>Jenis Perkara</td><td style='padding: 8px; border-bottom: 1px solid #f1f5f9;'>" . htmlspecialchars($perkara->jenis_perkara ?? '-') . "</td></tr>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; color: #64748b;'>Hakim</td><td style='padding: 8px; border-bottom: 1px solid #f1f5f9;'>" . htmlspecialchars($perkara->nama_hakim ?? '-') . "</td></tr>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; color: #64748b;'>Batas Waktu Mediasi</td><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; font-weight: bold; color: #dc2626;'>{$tgl_batas}</td></tr>
                </table>
                <div style='margin: 25px 0 15px 0; text-align: center;'>
                    <a href='{$login_url}' style='display: inline-block; background-color: #0f766e; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-weight: bold; font-size: 13px;'>
                        📅 Login & Buat Jadwal Mediasi
                    </a>
                </div>
                <hr style='border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;' />
                <p style='font-size: 11px; color: #94a3b8; margin: 0;'>Pesan ini dikirimkan secara otomatis oleh Sistem Informasi Mediasi {$satker}.</p>
            </div>
        </div>
        ";

        $this->kirim($email, $subject, $html_body, $perkara_id);
    }

    /**
     * Kirim notifikasi pemberhentian/penggantian penugasan perkara ke mediator lama.
     */
    public function kirim_penggantian_mediator($perkara_id, $old_mediator_id) {
        if (!$this->is_enabled()) return;

        $this->CI->load->model(['M_perkara', 'M_mediator']);
        $perkara  = $this->CI->M_perkara->get_by_id($perkara_id);
        $mediator = $this->CI->M_mediator->get_by_id($old_mediator_id);

        if (!$perkara || !$mediator) return;

        $email = trim($mediator->email ?? '');
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) return;

        $satker   = get_app_setting('nama_satker', 'Pengadilan Agama Gorontalo');
        $app_name = get_app_setting('nama_aplikasi', 'SIPO-MEDIASI');
        $subject  = "[PENGGANTIAN MEDIASI] Perkara No. {$perkara->nomor_perkara} - {$satker}";

        $html_body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px;'>
            <div style='background-color: #b91c1c; color: #ffffff; padding: 15px 20px; border-radius: 8px 8px 0 0;'>
                <h2 style='margin: 0; font-size: 18px;'>⚠️ PENGGANTIAN PENUGASAN MEDIASI</h2>
                <p style='margin: 5px 0 0 0; font-size: 12px; color: #fca5a5;'>{$satker} — {$app_name}</p>
            </div>
            <div style='padding: 20px; background-color: #ffffff;'>
                <p>Yth. <strong>" . htmlspecialchars($mediator->nama) . "</strong>,</p>
                <p>Diberitahukan bahwa penugasan Anda sebagai Mediator untuk perkara berikut telah <strong>DIGANTIKAN / DICABUT</strong> oleh Panitera Pengganti (PP) / Admin.</p>
                <table style='width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 13px;'>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 160px;'>Nomor Perkara</td><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; font-weight: bold;'>{$perkara->nomor_perkara}</td></tr>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; color: #64748b;'>Jenis Perkara</td><td style='padding: 8px; border-bottom: 1px solid #f1f5f9;'>" . htmlspecialchars($perkara->jenis_perkara ?? '-') . "</td></tr>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; color: #64748b;'>Status Penugasan</td><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; font-weight: bold; color: #dc2626;'>Dioperkan ke Mediator Lain</td></tr>
                </table>
                <p style='font-size: 12px; color: #64748b;'>Perkara ini sudah tidak muncul di daftar penugasan aktif Anda. Terima kasih atas dedikasi Anda.</p>
                <hr style='border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;' />
                <p style='font-size: 11px; color: #94a3b8; margin: 0;'>Pesan ini dikirimkan secara otomatis oleh Sistem Informasi Mediasi {$satker}.</p>
            </div>
        </div>
        ";

        $this->kirim($email, $subject, $html_body, $perkara_id);
    }
}

