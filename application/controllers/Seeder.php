<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Seeder Controller — Mengatur Ulang & Mengisi Data Awal Pegawai & Mediator
 * Memastikan tidak ada nama duplikat, 1 admin, dan password default 'password'
 */
class Seeder extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function index() {
        $this->run();
    }

    public function run() {
        $default_hash = password_hash('password', PASSWORD_BCRYPT);

        // 1. Data Pegawai / User Unik (Tanpa Duplikat)
        $users_data = [
            // Admin (1 User)
            [
                'username'  => 'admin',
                'password'  => $default_hash,
                'nama'      => 'Administrator Mediasi',
                'role'      => 'admin',
                'id_sipp'   => null,
                'nip'       => null,
                'is_active' => 1
            ],

            // Hakim Mediator
            [
                'username'  => '196807031992021001',
                'password'  => $default_hash,
                'nama'      => 'Abdul Hakim, S.Ag., S.H., M.H.',
                'role'      => 'hakim',
                'id_sipp'   => '42',
                'nip'       => '196807031992021001',
                'is_active' => 1
            ],
            [
                'username'  => '196610121992032002',
                'password'  => $default_hash,
                'nama'      => 'Dra. Mukasipa, M.H.',
                'role'      => 'hakim',
                'id_sipp'   => '38',
                'nip'       => '196610121992032002',
                'is_active' => 1
            ],
            [
                'username'  => '196601011993031011',
                'password'  => $default_hash,
                'nama'      => 'Drs. Satrio AM. Karim',
                'role'      => 'hakim',
                'id_sipp'   => '34',
                'nip'       => '196601011993031011',
                'is_active' => 1
            ],
            [
                'username'  => '197906132006041003',
                'password'  => $default_hash,
                'nama'      => 'Dr. Mukhtaruddin Bahrum, S.H.I., M.H.I.',
                'role'      => 'hakim',
                'id_sipp'   => '40',
                'nip'       => '197906132006041003',
                'is_active' => 1
            ],
            [
                'username'  => '197211052005021002',
                'password'  => $default_hash,
                'nama'      => 'Muhamad Anwar Umar, S.Ag.',
                'role'      => 'hakim',
                'id_sipp'   => '39',
                'nip'       => '197211052005021002',
                'is_active' => 1
            ],

            // Mediator Non-Hakim
            [
                'username'  => 'mediator_yusuf',
                'password'  => $default_hash,
                'nama'      => 'Muhammad Yusuf Putra, M.H., CPM',
                'role'      => 'mediator',
                'id_sipp'   => '44',
                'nip'       => null,
                'is_active' => 1
            ],
            [
                'username'  => 'mediator_hasnia',
                'password'  => $default_hash,
                'nama'      => 'Hasnia, S.H.I., M.H., CLA., CPM.',
                'role'      => 'mediator',
                'id_sipp'   => '43',
                'nip'       => null,
                'is_active' => 1
            ],

            // Panitera Pengganti (PP)
            [
                'username'  => '198310102006042001',
                'password'  => $default_hash,
                'nama'      => 'Rinda Wanni, S.H., M.H.',
                'role'      => 'pp',
                'id_sipp'   => '43',
                'nip'       => '198310102006042001',
                'is_active' => 1
            ],
            [
                'username'  => '197109181998031003',
                'password'  => $default_hash,
                'nama'      => 'Muhiddin Litti, S.Ag., M.H.I.',
                'role'      => 'pp',
                'id_sipp'   => '31',
                'nip'       => '197109181998031003',
                'is_active' => 1
            ],
            [
                'username'  => '197106301998032001',
                'password'  => $default_hash,
                'nama'      => 'Luthfiyah, S.Ag, M.H',
                'role'      => 'pp',
                'id_sipp'   => '20',
                'nip'       => '197106301998032001',
                'is_active' => 1
            ],
            [
                'username'  => '197902072009121004',
                'password'  => $default_hash,
                'nama'      => 'Haryono Daud, S.H.I., M.H.',
                'role'      => 'pp',
                'id_sipp'   => '44',
                'nip'       => '197902072009121004',
                'is_active' => 1
            ],
            [
                'username'  => '198010132009122001',
                'password'  => $default_hash,
                'nama'      => 'Alinda Ahmad Ishak, S.H.I., M.H.',
                'role'      => 'pp',
                'id_sipp'   => '45',
                'nip'       => '198010132009122001',
                'is_active' => 1
            ],
            [
                'username'  => '197811072009122002',
                'password'  => $default_hash,
                'nama'      => 'Mardiana Abubakar, S.H.I., M.H.',
                'role'      => 'pp',
                'id_sipp'   => '42',
                'nip'       => '197811072009122002',
                'is_active' => 1
            ],
            [
                'username'  => '197107031998031006',
                'password'  => $default_hash,
                'nama'      => 'Djarnawi H. Datau, S.Ag.',
                'role'      => 'pp',
                'id_sipp'   => '27',
                'nip'       => '197107031998031006',
                'is_active' => 1
            ],
            [
                'username'  => '196711271992021001',
                'password'  => $default_hash,
                'nama'      => 'Suratman Nang, S.H.',
                'role'      => 'pp',
                'id_sipp'   => '40',
                'nip'       => '196711271992021001',
                'is_active' => 1
            ],
            [
                'username'  => '197806152006042013',
                'password'  => $default_hash,
                'nama'      => 'Isma Katili, S.Ag.',
                'role'      => 'pp',
                'id_sipp'   => '29',
                'nip'       => '197806152006042013',
                'is_active' => 1
            ],
            [
                'username'  => '196712021987031001',
                'password'  => $default_hash,
                'nama'      => 'Irsan Masri, S.H.I.',
                'role'      => 'pp',
                'id_sipp'   => '35',
                'nip'       => '196712021987031001',
                'is_active' => 1
            ],
            [
                'username'  => '198410062009042006',
                'password'  => $default_hash,
                'nama'      => 'Nizma Rizky Datau, S.H.I.',
                'role'      => 'pp',
                'id_sipp'   => '30',
                'nip'       => '198410062009042006',
                'is_active' => 1
            ],
            [
                'username'  => '197307272006042001',
                'password'  => $default_hash,
                'nama'      => 'Dorkas Eremst Yunginger, S.H.I., M.H.',
                'role'      => 'pp',
                'id_sipp'   => '48',
                'nip'       => '197307272006042001',
                'is_active' => 1
            ],
            [
                'username'  => '197209032003122002',
                'password'  => $default_hash,
                'nama'      => 'Misrawati Tululi, S.Ag., S.H.',
                'role'      => 'pp',
                'id_sipp'   => '47',
                'nip'       => '197209032003122002',
                'is_active' => 1
            ],
            [
                'username'  => '197710101998032002',
                'password'  => $default_hash,
                'nama'      => 'Nurhayati Hasan, S.H.I., M.H.',
                'role'      => 'pp',
                'id_sipp'   => '51',
                'nip'       => '197710101998032002',
                'is_active' => 1
            ],
            [
                'username'  => '198503282011012019',
                'password'  => $default_hash,
                'nama'      => 'Nurhayati Mustapa Hasan, S.H., M.H.',
                'role'      => 'pp',
                'id_sipp'   => '50',
                'nip'       => '198503282011012019',
                'is_active' => 1
            ],
            [
                'username'  => '198111242007041001',
                'password'  => $default_hash,
                'nama'      => 'Nuryadin Akuba, S.H.I.',
                'role'      => 'pp',
                'id_sipp'   => '49',
                'nip'       => '198111242007041001',
                'is_active' => 1
            ],
            [
                'username'  => '197803252003121003',
                'password'  => $default_hash,
                'nama'      => 'Tamrin Yunus, S.Ag. M.H.',
                'role'      => 'pp',
                'id_sipp'   => '46',
                'nip'       => '197803252003121003',
                'is_active' => 1
            ],
        ];

        $this->db->trans_begin();

        // Temporary disable FK checks
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");

        // Sync data user (Upsert berdasarkan username/NIP)
        $user_id_map = [];

        foreach ($users_data as $u) {
            $existing = $this->db->get_where('users', ['username' => $u['username']])->row();
            if ($existing) {
                $this->db->where('id', $existing->id)->update('users', [
                    'password'  => $u['password'],
                    'nama'      => $u['nama'],
                    'role'      => $u['role'],
                    'id_sipp'   => $u['id_sipp'],
                    'nip'       => $u['nip'],
                    'is_active' => 1
                ]);
                $user_id_map[$u['username']] = $existing->id;
            } else {
                $this->db->insert('users', $u);
                $user_id_map[$u['username']] = $this->db->insert_id();
            }
        }

        // Hapus user duplikat lama yang tidak terdaftar di $users_data (seperti 'yono', 'yusuf', 'hasnia')
        $valid_usernames = array_column($users_data, 'username');
        $this->db->where_not_in('username', $valid_usernames)->delete('users');

        // 2. Data Mediator Unik (Hakim & Non-Hakim dari API SIPP)
        $mediators_data = [
            // Mediator Hakim
            [
                'username_user' => '196807031992021001',
                'id_mediator'   => '42',
                'nama'          => 'Abdul Hakim, S.Ag., S.H., M.H.',
                'jenis'         => 'hakim',
                'no_sertifikat' => 'SERTIF/HKM/42',
            ],
            [
                'username_user' => '196610121992032002',
                'id_mediator'   => '38',
                'nama'          => 'Dra. Mukasipa, M.H.',
                'jenis'         => 'hakim',
                'no_sertifikat' => 'SERTIF/HKM/38',
            ],
            [
                'username_user' => '196601011993031011',
                'id_mediator'   => '34',
                'nama'          => 'Drs. Satrio AM. Karim',
                'jenis'         => 'hakim',
                'no_sertifikat' => 'SERTIF/HKM/34',
            ],
            [
                'username_user' => '197906132006041003',
                'id_mediator'   => '40',
                'nama'          => 'Dr. Mukhtaruddin Bahrum, S.H.I., M.H.I.',
                'jenis'         => 'hakim',
                'no_sertifikat' => 'SERTIF/HKM/40',
            ],
            [
                'username_user' => '197211052005021002',
                'id_mediator'   => '39',
                'nama'          => 'Muhamad Anwar Umar, S.Ag.',
                'jenis'         => 'hakim',
                'no_sertifikat' => 'SERTIF/HKM/39',
            ],

            // Mediator Non-Hakim
            [
                'username_user' => 'mediator_yusuf',
                'id_mediator'   => '44',
                'nama'          => 'Muhammad Yusuf Putra, M.H., CPM',
                'jenis'         => 'non_hakim',
                'no_sertifikat' => '127/KMA/SK/VIII/2020',
            ],
            [
                'username_user' => 'mediator_hasnia',
                'id_mediator'   => '43',
                'nama'          => 'Hasnia, S.H.I., M.H., CLA., CPM.',
                'jenis'         => 'non_hakim',
                'no_sertifikat' => '089/KMA/SK/VI/2021',
            ],
        ];

        // Reset & Seed Tabel Mediators
        $this->db->query("TRUNCATE TABLE mediators;");

        $inserted_m = 0;
        foreach ($mediators_data as $m) {
            $user_id = isset($user_id_map[$m['username_user']]) ? $user_id_map[$m['username_user']] : null;
            $this->db->insert('mediators', [
                'user_id'       => $user_id,
                'id_mediator'   => $m['id_mediator'],
                'nama'          => $m['nama'],
                'jenis'         => $m['jenis'],
                'no_sertifikat' => $m['no_sertifikat'],
                'is_active'     => 1
            ]);
            $inserted_m++;
        }

        // Re-enable FK checks
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo "Gagal menjalankan Database Seeder!";
        } else {
            $this->db->trans_commit();
            echo "<h1>Database Seeding Berhasil!</h1>";
            echo "<p>Total User: " . count($users_data) . " (Admin: 1, Password default semua: 'password')</p>";
            echo "<p>Total Mediator: " . $inserted_m . " (Hakim & Non-Hakim)</p>";
            echo "<p>User duplikat telah dibersihkan sepenuhnya.</p>";
        }
    }
}
