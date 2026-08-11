<?php
/**
 * Seeder Pegawai SIPP ke mediasi_db (users & mediators)
 */

define('CRON_EXECUTION', true);
chdir(__DIR__);

$_SERVER['HTTP_HOST']       = 'localhost';
$_SERVER['SERVER_NAME']     = 'localhost';
$_SERVER['REMOTE_ADDR']     = '127.0.0.1';
$_SERVER['REQUEST_METHOD']  = 'GET';

ob_start();
require_once __DIR__ . '/index.php';
ob_end_clean();

$CI =& get_instance();
$db = $CI->db;

echo "=======================================================\n";
echo " SIPO-MEDIASI — Pegawai SIPP Seeder Tool\n";
echo "=======================================================\n";

// 1. Tambah Kolom id_sipp & nip pada tabel users dan mediators (jika belum ada)
$db->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS id_sipp VARCHAR(50) NULL AFTER role, ADD COLUMN IF NOT EXISTS nip VARCHAR(50) NULL AFTER id_sipp;");
$db->query("ALTER TABLE mediators ADD COLUMN IF NOT EXISTS id_sipp VARCHAR(50) NULL AFTER no_sertifikat;");

echo "[OK] Struktur database diperbarui.\n";

$default_password = password_hash('password123', PASSWORD_BCRYPT);

// Data dari data_pegawai.sql
$pegawai = [
    // --- HAKIM, KETUA, WAKIL ---
    [
        'nama'    => 'Dra. Mukasipa, M.H.',
        'nip'     => '196610121992032002',
        'jabatan' => 'Hakim Tingkat Pertama',
        'role'    => 'hakim',
        'id_sipp' => '38',
    ],
    [
        'nama'    => 'Drs. Satrio AM. Karim',
        'nip'     => '196601011993031011',
        'jabatan' => 'Hakim Tingkat Pertama',
        'role'    => 'hakim',
        'id_sipp' => '34',
    ],
    [
        'nama'    => 'Abdul Hakim, S.Ag., S.H., M.H.',
        'nip'     => '196807031992021001',
        'jabatan' => 'Ketua Pengadilan',
        'role'    => 'hakim',
        'id_sipp' => '42',
    ],
    [
        'nama'    => 'Dr. Mukhtaruddin Bahrum, S.H.I., M.H.I.',
        'nip'     => '197906132006041003',
        'jabatan' => 'Wakil Ketua Pengadilan',
        'role'    => 'hakim',
        'id_sipp' => '40',
    ],
    [
        'nama'    => 'Muhamad Anwar Umar, S.Ag.',
        'nip'     => '197211052005021002',
        'jabatan' => 'Hakim Tingkat Pertama',
        'role'    => 'hakim',
        'id_sipp' => '39',
    ],

    // --- PANITERA, PANITERA MUDA, PANITERA PENGGANTI (TENAGA TEKNIS) ---
    [
        'nama'    => 'Muhiddin Litti, S.Ag., M.H.I.',
        'nip'     => '197109181998031003',
        'jabatan' => 'Panitera Tingkat Pertama',
        'role'    => 'pp',
        'id_sipp' => '31',
    ],
    [
        'nama'    => 'Luthfiyah, S.Ag, M.H',
        'nip'     => '197106301998032001',
        'jabatan' => 'Panitera Muda',
        'role'    => 'pp',
        'id_sipp' => '20',
    ],
    [
        'nama'    => 'Haryono Daud, S.H.I.,M.H.',
        'nip'     => '197902072009121004',
        'jabatan' => 'Panitera Pengganti',
        'role'    => 'pp',
        'id_sipp' => '44',
    ],
    [
        'nama'    => 'Alinda Ahmad Ishak, S.H.I., M.H.',
        'nip'     => '198010132009122001',
        'jabatan' => 'Panitera Pengganti',
        'role'    => 'pp',
        'id_sipp' => '45',
    ],
    [
        'nama'    => 'Mardiana Abubakar, S.H.I.,M.H.',
        'nip'     => '197811072009122002',
        'jabatan' => 'Panitera Muda',
        'role'    => 'pp',
        'id_sipp' => '42',
    ],
    [
        'nama'    => 'Djarnawi H. Datau, S.Ag.',
        'nip'     => '197107031998031006',
        'jabatan' => 'Panitera Pengganti',
        'role'    => 'pp',
        'id_sipp' => '27',
    ],
    [
        'nama'    => 'Suratman Nang, S.H.',
        'nip'     => '196711271992021001',
        'jabatan' => 'Panitera Pengganti',
        'role'    => 'pp',
        'id_sipp' => '40',
    ],
    [
        'nama'    => 'Isma Katili, S.Ag.',
        'nip'     => '197806152006042013',
        'jabatan' => 'Panitera Pengganti',
        'role'    => 'pp',
        'id_sipp' => '29',
    ],
    [
        'nama'    => 'Irsan Masri, S.H.I.',
        'nip'     => '196712021987031001',
        'jabatan' => 'Panitera Muda',
        'role'    => 'pp',
        'id_sipp' => '35',
    ],
    [
        'nama'    => 'Nizma Rizky Datau, S.H.I.',
        'nip'     => '198410062009042006',
        'jabatan' => 'Panitera Pengganti',
        'role'    => 'pp',
        'id_sipp' => '30',
    ],
    [
        'nama'    => 'Rinda Wanni, S.H., M.H.',
        'nip'     => '198310102006042001',
        'jabatan' => 'Panitera Pengganti',
        'role'    => 'pp',
        'id_sipp' => '43',
    ],
    [
        'nama'    => 'Dorkas Eremst Yunginger, S.H.I., M.H.',
        'nip'     => '197307272006042001',
        'jabatan' => 'Panitera Pengganti',
        'role'    => 'pp',
        'id_sipp' => '48',
    ],
    [
        'nama'    => 'Misrawati Tululi, S.Ag., S.H.',
        'nip'     => '197209032003122002',
        'jabatan' => 'Panitera Pengganti',
        'role'    => 'pp',
        'id_sipp' => '47',
    ],
    [
        'nama'    => 'Nurhayati Hasan, S.H.I., M.H.',
        'nip'     => '197710101998032002',
        'jabatan' => 'Panitera Pengganti',
        'role'    => 'pp',
        'id_sipp' => '51',
    ],
    [
        'nama'    => 'Nurhayati Mustapa Hasan, S.H., M.H.',
        'nip'     => '198503282011012019',
        'jabatan' => 'Panitera Pengganti',
        'role'    => 'pp',
        'id_sipp' => '50',
    ],
    [
        'nama'    => 'Nuryadin Akuba, S.H.I.',
        'nip'     => '198111242007041001',
        'jabatan' => 'Panitera Pengganti',
        'role'    => 'pp',
        'id_sipp' => '49',
    ],
    [
        'nama'    => 'Tamrin Yunus, S.Ag. M.H.',
        'nip'     => '197803252003121003',
        'jabatan' => 'Panitera Pengganti',
        'role'    => 'pp',
        'id_sipp' => '46',
    ],
];

$user_inserted = 0;
$user_updated  = 0;
$mediator_inserted = 0;

foreach ($pegawai as $p) {
    $username = $p['nip'];
    
    // Check if user exists by NIP or username
    $exist_user = $db->get_where('users', ['username' => $username])->row();
    if (!$exist_user) {
        $exist_user = $db->get_where('users', ['nip' => $p['nip']])->row();
    }
    if (!$exist_user) {
        $exist_user = $db->get_where('users', ['nama' => $p['nama']])->row();
    }

    $user_data = [
        'nama'      => $p['nama'],
        'username'  => $username,
        'role'      => $p['role'],
        'id_sipp'   => $p['id_sipp'],
        'nip'       => $p['nip'],
        'is_active' => 1
    ];

    if ($exist_user) {
        $db->where('id', $exist_user->id)->update('users', $user_data);
        $user_id = $exist_user->id;
        $user_updated++;
    } else {
        $user_data['password'] = $default_password;
        $db->insert('users', $user_data);
        $user_id = $db->insert_id();
        $user_inserted++;
    }

    // Jika role == 'hakim', tambahkan / cocokkan ke tabel mediators
    if ($p['role'] === 'hakim') {
        $exist_mediator = $db->get_where('mediators', ['nama' => $p['nama']])->row();
        if (!$exist_mediator) {
            $exist_mediator = $db->get_where('mediators', ['id_sipp' => $p['id_sipp']])->row();
        }

        $mediator_data = [
            'user_id'       => $user_id,
            'nama'          => $p['nama'],
            'jenis'         => 'hakim',
            'no_sertifikat' => 'SERTIF/HKM/' . $p['id_sipp'],
            'id_sipp'       => $p['id_sipp'],
            'is_active'     => 1
        ];

        if ($exist_mediator) {
            $db->where('id', $exist_mediator->id)->update('mediators', $mediator_data);
        } else {
            $db->insert('mediators', $mediator_data);
            $mediator_inserted++;
        }
    }
}

echo "=======================================================\n";
echo " SEED BERHASIL SELESAI!\n";
echo " - User Baru Ditambahkan : {$user_inserted}\n";
echo " - User Diperbarui      : {$user_updated}\n";
echo " - Mediator Baru         : {$mediator_inserted}\n";
echo " - Default Password      : password123\n";
echo "=======================================================\n";
