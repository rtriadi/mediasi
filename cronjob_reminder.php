<?php
/**
 * cronjob_reminder.php — Script Pengingat H-1 Sesi Mediasi
 * ============================================================
 * Jalankan via CLI: php c:\xampp\htdocs\mediasi\cronjob_reminder.php
 *
 * Konfigurasi Cron (Linux/cPanel):
 *   0 8 * * * php /var/www/html/mediasi/cronjob_reminder.php >> /var/log/mediasi_reminder.log 2>&1
 *
 * Konfigurasi Task Scheduler (Windows):
 *   Program: php.exe | Arguments: C:\xampp\htdocs\mediasi\cronjob_reminder.php
 */

define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
define('BASEPATH', FCPATH . 'system' . DIRECTORY_SEPARATOR);
define('APPPATH', FCPATH . 'application' . DIRECTORY_SEPARATOR);

$config_db_path = APPPATH . 'config/database.php';
if (!file_exists($config_db_path)) {
    die("[ERROR] File database.php tidak ditemukan.\n");
}

$db = []; $active_group = 'default';
require $config_db_path;
$cfg = $db[$active_group];

$conn = new mysqli($cfg['hostname'], $cfg['username'], $cfg['password'], $cfg['database'],
    isset($cfg['port']) && $cfg['port'] ? (int)$cfg['port'] : 3306);
if ($conn->connect_error) die("[ERROR] Koneksi DB gagal: " . $conn->connect_error . "\n");
$conn->set_charset('utf8mb4');

$tgl_besok = date('Y-m-d', strtotime('+1 day'));
echo "[INFO] ============================================\n";
echo "[INFO] SIPO-MEDIASI — Reminder H-1\n";
echo "[INFO] Waktu: " . date('Y-m-d H:i:s') . "\n";
echo "[INFO] Mencari sesi mediasi pada: {$tgl_besok}\n";
echo "[INFO] ============================================\n";

// Ambil settings
$settings = [];
$res = $conn->query("SELECT setting_key, setting_value FROM settings");
if ($res) { while ($row = $res->fetch_assoc()) $settings[$row['setting_key']] = $row['setting_value']; }

$satker   = $settings['nama_satker']  ?? 'Pengadilan Agama Gorontalo';
$app_name = $settings['nama_aplikasi'] ?? 'SIPO-MEDIASI';
$wa_enabled    = ($settings['wa_notif_active'] ?? '0') === '1';
$email_enabled = ($settings['email_notif_active'] ?? '1') === '1';
$wa_token   = $settings['wa_api_token'] ?? '';
$wa_api_url = $settings['wa_api_url']   ?? 'https://api.fonnte.com/send';
$smtp_user  = $settings['smtp_user']   ?? '';

// Ambil sesi besok
$sql = "SELECT s.*, p.nomor_perkara, jp.nama AS jenis_perkara, m.nama AS nama_mediator, r.nama_ruangan
        FROM sesi_mediasi s
        JOIN perkara p ON p.id = s.perkara_id
        LEFT JOIN jenis_perkara jp ON jp.id = p.jenis_perkara_id
        LEFT JOIN perkara_mediator pm ON pm.perkara_id = p.id
        LEFT JOIN mediators m ON m.id = pm.mediator_id
        LEFT JOIN ruangan r ON r.id = s.ruangan_id
        WHERE s.tgl_mediasi = ? AND s.status_sesi NOT IN ('batal', 'dijadwal_ulang')";

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $tgl_besok);
$stmt->execute();
$sesi_list = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (empty($sesi_list)) {
    echo "[INFO] Tidak ada sesi mediasi terjadwal untuk besok.\n";
    $conn->close(); exit(0);
}

echo "[INFO] Ditemukan " . count($sesi_list) . " sesi untuk besok.\n\n";

$bln_list  = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$hari_list = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];

foreach ($sesi_list as $sesi) {
    echo "[SESI] Perkara: {$sesi['nomor_perkara']} | {$sesi['tgl_mediasi']} {$sesi['jam_mulai']}\n";

    $stmt2 = $conn->prepare("SELECT nama, email, no_hp, jenis FROM perkara_pihak WHERE perkara_id = ?");
    $stmt2->bind_param('i', $sesi['perkara_id']);
    $stmt2->execute();
    $pihak_list = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt2->close();

    if (!empty($sesi['link_virtual'])) {
        $tempat = ($sesi['platform_virtual'] ?: 'Virtual') . ' — Online';
    } elseif (!empty($sesi['nama_ruangan'])) {
        $tempat = $sesi['nama_ruangan'] . " ({$satker})";
    } else {
        $tempat = $sesi['tempat_lain'] ?: 'Konfirmasi ke Mediator';
    }

    $ts = strtotime($sesi['tgl_mediasi']);
    $tgl_indo = $hari_list[date('w',$ts)] . ', ' . date('d',$ts) . ' ' . $bln_list[(int)date('m',$ts)-1] . ' ' . date('Y',$ts);
    $jam_str  = substr($sesi['jam_mulai'],0,5) . ' - ' . substr($sesi['jam_selesai'],0,5);

    foreach ($pihak_list as $p) {
        // WA
        if ($wa_enabled && !empty($p['no_hp']) && !empty($wa_token)) {
            $virtual_wa = !empty($sesi['link_virtual']) ? "\n\nLink Meeting:\n{$sesi['link_virtual']}" : '';
            $pesan = "PENGINGAT MEDIASI BESOK - {$satker}\n\nYth. {$p['nama']},\nSesi mediasi BESOK:\n\n" .
                     "Perkara : {$sesi['nomor_perkara']}\nTanggal : {$tgl_indo}\nWaktu   : {$jam_str} WITA\n" .
                     "Tempat  : {$tempat}\nMediator: {$sesi['nama_mediator']}{$virtual_wa}\n\n" .
                     "Harap hadir tepat waktu membawa KTP.\n---\n{$satker}";
            $no = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $p['no_hp']));
            $ch = curl_init($wa_api_url);
            curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_POST=>true,
                CURLOPT_POSTFIELDS=>['target'=>$no,'message'=>$pesan],
                CURLOPT_HTTPHEADER=>['Authorization: '.$wa_token], CURLOPT_TIMEOUT=>10]);
            $ok = curl_exec($ch); curl_close($ch);
            echo "  [WA] {$p['nama']} ({$p['no_hp']}): " . ($ok ? "TERKIRIM" : "GAGAL") . "\n";
        }

        // Email
        if ($email_enabled && !empty($p['email']) && !empty($smtp_user)) {
            $virtual_email = !empty($sesi['link_virtual']) ? "<p>Link Meeting: <a href='{$sesi['link_virtual']}'>{$sesi['link_virtual']}</a></p>" : '';
            $subj = "Pengingat Mediasi Besok: {$sesi['nomor_perkara']}";
            $body = "<p>Yth. <b>{$p['nama']}</b>,</p><p>Pengingat sesi mediasi <b>BESOK</b>:</p>
                     <ul><li>Perkara: {$sesi['nomor_perkara']}</li><li>Tanggal: {$tgl_indo}</li>
                     <li>Waktu: {$jam_str} WITA</li><li>Tempat: {$tempat}</li>
                     <li>Mediator: {$sesi['nama_mediator']}</li></ul>{$virtual_email}
                     <p>Harap hadir tepat waktu membawa KTP.</p><p><i>{$app_name} &mdash; {$satker}</i></p>";
            $headers = "MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8\r\nFrom: {$app_name} <{$smtp_user}>\r\n";
            $ok = @mail($p['email'], $subj, $body, $headers);
            echo "  [EMAIL] {$p['nama']} ({$p['email']}): " . ($ok ? "TERKIRIM" : "GAGAL") . "\n";
        }
    }
    echo "\n";
}

$conn->close();
echo "[INFO] Selesai — Reminder H-1 berhasil diproses.\n";
echo "[INFO] ============================================\n";
