<?php
/**
 * Cronjob API Sync SIPP SIPO-MEDIASI PA Gorontalo
 * -----------------------------------------------------------
 * Script CLI untuk otomatisasi sinkronisasi data dari API SIPP
 * Dapat dipasang pada Task Scheduler (Windows) atau Crontab (Linux).
 * Contoh penggunaan: php cronjob_api_sync.php
 */

define('CRON_EXECUTION', true);

// Set PHP CLI Environment
if (php_sapi_name() !== 'cli') {
    die("Akses ditolak: Script ini hanya dapat dijalankan melalui CLI (Command Line Interface).\n");
}

chdir(__DIR__);

// Mock HTTP Host and Server parameters for CI CLI execution
$_SERVER['HTTP_HOST']       = 'localhost';
$_SERVER['SERVER_NAME']     = 'localhost';
$_SERVER['REMOTE_ADDR']     = '127.0.0.1';
$_SERVER['REQUEST_METHOD']  = 'GET';

// Load CodeIgniter framework
ob_start();
require_once __DIR__ . '/index.php';
ob_end_clean();

$CI =& get_instance();
$CI->load->library('SippApi', null, 'sippapi');
$CI->load->model('M_pengaturan');

$settings = $CI->M_pengaturan->get_all_as_array();
$auto_active = isset($settings['api_sync_auto']) ? $settings['api_sync_auto'] : '1';

echo "=======================================================\n";
echo " SIPO-MEDIASI — SIPP API Auto-Sync Task\n";
echo " Waktu Eksekusi: " . date('Y-m-d H:i:s') . "\n";
echo "=======================================================\n";

if ($auto_active !== '1') {
    echo "[INFO] Auto-sync non-aktif pada pengaturan aplikasi.\n";
    exit(0);
}

echo "[RUNNING] Memulai sinkronisasi data dari SIPP API...\n";

$result = $CI->sippapi->sync();

if (isset($result['status']) && $result['status'] === 'success') {
    echo "[SUCCESS] " . $result['message'] . "\n";
    if (isset($result['sync'])) {
        echo "  - Perkara Baru (Inserted)  : " . $result['sync']['inserted'] . "\n";
        echo "  - Perkara Updated          : " . $result['sync']['updated'] . "\n";
        echo "  - Perkara Gagal            : " . $result['sync']['failed'] . "\n";
    }
} else {
    echo "[ERROR] " . (isset($result['message']) ? $result['message'] : 'Gagal sinkronisasi API') . "\n";
}

echo "=======================================================\n";
echo " Selesai.\n";
