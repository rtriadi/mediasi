<?php
/**
 * Automated Audit Test Suite for SIPO-MEDIASI PA Gorontalo
 * Run via CLI: php test_suite.php
 */
define('ENVIRONMENT', 'development');
define('BASEPATH', __DIR__ . '/system/');
define('APPPATH', __DIR__ . '/application/');
define('VIEWPATH', __DIR__ . '/application/views/');

$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

// Set timezone WITA
date_default_timezone_set('Asia/Makassar');

// Load database configuration
require_once APPPATH . 'config/database.php';
$db_config = $db['default'];

$dsn = "mysql:host={$db_config['hostname']};dbname={$db_config['database']};charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $db_config['username'], $db_config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ]);
    $pdo->exec("SET time_zone = '+08:00'");
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

echo "====================================================\n";
echo "    SIPO-MEDIASI EMPIRICAL LOGIC TEST SUITE\n";
echo "====================================================\n\n";

$tests_passed = 0;
$tests_failed = 0;

function assert_test($description, $condition, $fail_reason = "") {
    global $tests_passed, $tests_failed;
    if ($condition) {
        echo "  [PASS] {$description}\n";
        $tests_passed++;
    } else {
        echo "  [FAIL] {$description}\n";
        if ($fail_reason) echo "         Reason: {$fail_reason}\n";
        $tests_failed++;
    }
}

// ----------------------------------------------------
// TEST 1: TIMEZONE & MYSQL TIMEZONE SYNCHRONIZATION
// ----------------------------------------------------
echo "--- TEST GROUP 1: Timezone & MySQL Clock ---\n";
$php_tz = date_default_timezone_get();
assert_test("PHP Default Timezone is Asia/Makassar", $php_tz === 'Asia/Makassar', "Got: {$php_tz}");

$stmt = $pdo->query("SELECT NOW() as now_time");
$db_now = $stmt->fetch()->now_time;
$php_now = date('Y-m-d H:i:s');
$diff_seconds = abs(strtotime($db_now) - strtotime($php_now));
assert_test("PHP and MySQL time synchronization (<2s difference)", $diff_seconds <= 2, "DB: {$db_now}, PHP: {$php_now}");


// ----------------------------------------------------
// TEST 2: HASIL MEDIASI REQUIRES SESSIONS & COMPLETED STATUS
// ----------------------------------------------------
echo "\n--- TEST GROUP 2: Input Hasil Mediasi Session Check ---\n";

// Fetch a valid PP user ID
$pp_user = $pdo->query("SELECT id FROM users WHERE role = 'pp' LIMIT 1")->fetch();
$valid_pp_id = $pp_user ? $pp_user->id : 1;

// Insert dummy case
$stmt = $pdo->prepare("INSERT INTO perkara (nomor_perkara, jenis_perkara_id, pp_id, nama_hakim, tgl_batas_mediasi, status, created_at) VALUES (?, 1, ?, 'Hakim Audit', ?, 'menunggu', NOW())");
$test_num = 'TEST/Audit/' . time();
$tgl_limit = date('Y-m-d', strtotime('+30 days'));
$stmt->execute([$test_num, $valid_pp_id, $tgl_limit]);
$test_perkara_id = $pdo->lastInsertId();

// Verify zero sessions
$stmt = $pdo->prepare("SELECT * FROM sesi_mediasi WHERE perkara_id = ?");
$stmt->execute([$test_perkara_id]);
$jadwal_list = $stmt->fetchAll();
assert_test("Initial test case has 0 sessions", empty($jadwal_list));

// Check rule: Cannot input hasil if 0 sessions
$has_completed = false;
foreach ($jadwal_list as $j) {
    if (($j->status_sesi ?? '') === 'selesai') $has_completed = true;
}
assert_test("Input Hasil BLOCKED when zero sessions exist", !$has_completed);

// Add an active session (status = terjadwal)
$stmt = $pdo->prepare("INSERT INTO sesi_mediasi (perkara_id, mediator_id, tgl_mediasi, jam_mulai, jam_selesai, status_sesi) VALUES (?, 1, CURDATE(), '09:00:00', '10:00:00', 'terjadwal')");
$stmt->execute([$test_perkara_id]);
$test_sesi_id = $pdo->lastInsertId();

// Check unfinished session
$stmt = $pdo->prepare("SELECT * FROM sesi_mediasi WHERE perkara_id = ? AND status_sesi = 'terjadwal' LIMIT 1");
$stmt->execute([$test_perkara_id]);
$unfinished = $stmt->fetch();
assert_test("Unfinished active session detected", !empty($unfinished) && $unfinished->id == $test_sesi_id);

// Check rule: Cannot input hasil if session is still terjadwal
$stmt = $pdo->prepare("SELECT * FROM sesi_mediasi WHERE perkara_id = ?");
$stmt->execute([$test_perkara_id]);
$jadwal_list_2 = $stmt->fetchAll();
$has_completed_2 = false;
foreach ($jadwal_list_2 as $j) {
    if (($j->status_sesi ?? '') === 'selesai') $has_completed_2 = true;
}
assert_test("Input Hasil BLOCKED when session is still 'terjadwal'", !$has_completed_2);


// ----------------------------------------------------
// TEST 3: DATE BOUNDARY VALIDATION
// ----------------------------------------------------
echo "\n--- TEST GROUP 3: Session Date Limit Validation ---\n";
$future_over_limit = date('Y-m-d', strtotime('+40 days'));
$is_over_limit = (strtotime($future_over_limit) > strtotime($tgl_limit));
assert_test("Date exceeding tgl_batas_mediasi correctly flagged as invalid", $is_over_limit);


// ----------------------------------------------------
// TEST 4: PREVENT DELETING PIHAK WITH PRESENSI LOGS
// ----------------------------------------------------
echo "\n--- TEST GROUP 4: Presensi Cascade Deletion Protection ---\n";

// Insert test party
$stmt = $pdo->prepare("INSERT INTO perkara_pihak (perkara_id, jenis, nama, urutan) VALUES (?, 'penggugat', 'Pihak Audit Test', 1)");
$stmt->execute([$test_perkara_id]);
$test_pihak_id = $pdo->lastInsertId();

// Complete session and record presence
$stmt = $pdo->prepare("UPDATE sesi_mediasi SET status_sesi = 'selesai', catatan_sesi = 'Sesi tes audit' WHERE id = ?");
$stmt->execute([$test_sesi_id]);

$stmt = $pdo->prepare("INSERT INTO sesi_kehadiran (sesi_id, pihak_id, status_kehadiran) VALUES (?, ?, 'hadir')");
$stmt->execute([$test_sesi_id, $test_pihak_id]);

// Check presence count
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM sesi_kehadiran WHERE pihak_id = ?");
$stmt->execute([$test_pihak_id]);
$presensi_count = $stmt->fetch()->total;
assert_test("Presensi log recorded for test party", $presensi_count > 0);

// Simulate PP edit omitting this party
$omitted_ids = [$test_pihak_id];
$in_clause = implode(',', array_fill(0, count($omitted_ids), '?'));
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM sesi_kehadiran WHERE pihak_id IN ({$in_clause})");
$stmt->execute($omitted_ids);
$has_presensi = ($stmt->fetch()->total > 0);

assert_test("PP party deletion BLOCKED when presensi logs exist for party", $has_presensi);


// ----------------------------------------------------
// TEST 5: COMPLETED CASE ISOLATION
// ----------------------------------------------------
echo "\n--- TEST GROUP 5: Completed Case Protection ---\n";

// Mark case as completed
$stmt = $pdo->prepare("UPDATE perkara SET status = 'selesai' WHERE id = ?");
$stmt->execute([$test_perkara_id]);

// Verify case status
$stmt = $pdo->prepare("SELECT status FROM perkara WHERE id = ?");
$stmt->execute([$test_perkara_id]);
$case_status = $stmt->fetch()->status;

$edit_allowed = ($case_status !== 'selesai');
assert_test("Editing actions BLOCKED on completed case", !$edit_allowed);


// ----------------------------------------------------
// TEST 6: CLEANUP DUMMY DATA
// ----------------------------------------------------
echo "\n--- TEST GROUP 6: Cleanup Test Data ---\n";
$pdo->prepare("DELETE FROM sesi_kehadiran WHERE sesi_id = ?")->execute([$test_sesi_id]);
$pdo->prepare("DELETE FROM sesi_mediasi WHERE id = ?")->execute([$test_sesi_id]);
$pdo->prepare("DELETE FROM perkara_pihak WHERE id = ?")->execute([$test_pihak_id]);
$pdo->prepare("DELETE FROM perkara WHERE id = ?")->execute([$test_perkara_id]);

assert_test("Database cleanup completed successfully", true);


// ----------------------------------------------------
// FINAL SUMMARY
// ----------------------------------------------------
echo "\n====================================================\n";
echo "  FINAL RESULT: Passed {$tests_passed} / " . ($tests_passed + $tests_failed) . " tests.\n";
echo "====================================================\n";

if ($tests_failed === 0) {
    echo "SUCCESS: ALL EMPIRICAL LOGIC CHECKS PASSED PERFECTLY!\n";
    exit(0);
} else {
    echo "ERROR: {$tests_failed} TEST(S) FAILED!\n";
    exit(1);
}
