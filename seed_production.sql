-- =======================================================
-- SIPO-MEDIASI PA GORONTALO
-- Master Initial Seed Data for Production Launch
-- File: seed_production.sql
-- =======================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. TRUNCATE / RESET TRANSACTIONS DATA (DATA DUMMY DIPERBARUI)
TRUNCATE TABLE `perkara`;
TRUNCATE TABLE `perkara_mediator`;
TRUNCATE TABLE `perkara_mediator_log`;
TRUNCATE TABLE `perkara_pihak`;
TRUNCATE TABLE `sesi_mediasi`;
TRUNCATE TABLE `sesi_kehadiran`;
TRUNCATE TABLE `hasil_mediasi`;
TRUNCATE TABLE `log_notifikasi`;
TRUNCATE TABLE `ci_sessions`;

-- 2. RE-INITIALIZE MASTER TABLES
TRUNCATE TABLE `users`;
TRUNCATE TABLE `mediators`;
TRUNCATE TABLE `ruangan`;
TRUNCATE TABLE `jenis_perkara`;
TRUNCATE TABLE `settings`;

SET FOREIGN_KEY_CHECKS = 1;

-- =======================================================
-- MASTER CONFIGURATION (settings)
-- =======================================================
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('nama_aplikasi', 'SIPO-MEDIASI'),
('slogan_aplikasi', 'Sistem Informasi Pengelolaan Mediasi Perkara'),
('nama_satker', 'PA Gorontalo'),
('logo_aplikasi', 'logo_1785668354.png'),
('wa_notif_active', '0'),
('wa_api_url', 'https://api.fonnte.com/send'),
('wa_api_token', ''),
('email_notif_active', '1'),
('smtp_host', 'smtp.gmail.com'),
('smtp_port', '587'),
('smtp_user', 'surat@pa-gorontalo.go.id'),
('smtp_pass', 'fbnd xuga dejt xllk'),
('smtp_crypto', 'tls'),
('mail_from_name', 'SIPO-MEDIASI PA Gorontalo'),
('api_mediasi_url', 'http://192.168.100.5/perkara360/api/mediasi'),
('api_mediasi_key', ''),
('api_sync_auto', '1'),
('api_sync_interval_menit', '15'),
('batas_waktu_mediasi_hari', '30');

-- =======================================================
-- MASTER RUANGAN MEDIASI (ruangan)
-- =======================================================
INSERT INTO `ruangan` (`id`, `nama_ruangan`, `keterangan`, `is_active`) VALUES
(1, 'Ruang Mediasi Utama', 'Lantai 1 Gedung Utama Pengadilan Agama Gorontalo', 1),
(2, 'Ruang Mediasi 2', 'Lantai 1 Sayap Barat Pengadilan Agama Gorontalo', 1);

-- =======================================================
-- MASTER JENIS PERKARA (jenis_perkara)
-- =======================================================
INSERT INTO `jenis_perkara` (`id`, `nama`, `keterangan`, `is_active`) VALUES
(1, 'Cerai Gugat', 'Gugatan cerai yang diajukan istri (penggugat) kepada suami (tergugat). Wajib menempuh mediasi berdasarkan PERMA No. 1 Tahun 2016.', 1),
(2, 'Cerai Talak', 'Permohonan ikrar talak yang diajukan suami (pemohon) kepada istri (termohon). Wajib mediasi sesuai PERMA No. 1 Tahun 2016.', 1),
(3, 'Hak Asuh Anak (Hadhanah)', 'Sengketa hak pemeliharaan dan pengasuhan anak (hadhanah) pasca perceraian.', 1),
(4, 'Nafkah Iddah, Mut\'ah, dan Nafkah Anak', 'Sengketa kewajiban nafkah pasca perceraian.', 1),
(5, 'Harta Bersama (Gono-Gini)', 'Sengketa pembagian harta bersama (matrimonial property) dalam perkawinan.', 1),
(6, 'Poligami', 'Permohonan izin beristri lebih dari satu (poligami).', 1),
(7, 'Pengesahan / Penetapan Pernikahan (Isbat Nikah)', 'Permohonan pengesahan pernikahan yang tidak tercatat secara resmi di KUA.', 1),
(8, 'Kewarisan Islam', 'Sengketa pembagian harta warisan (tirkah) berdasarkan hukum Islam.', 1),
(9, 'Hibah', 'Sengketa keabsahan atau pelaksanaan pemberian harta secara sukarela.', 1),
(10, 'Wasiat', 'Sengketa pelaksanaan atau keabsahan wasiat.', 1),
(11, 'Wakaf', 'Sengketa peruntukan, pengelolaan, peralihan fungsi, atau keabsahan harta wakaf.', 1),
(12, 'Zakat / Infak / Sedekah', 'Sengketa pengelolaan atau penyaluran zakat, infak, dan sedekah.', 1),
(13, 'Sengketa Perbankan Syariah', 'Sengketa antara nasabah dan lembaga keuangan syariah atas akad pembiayaan.', 1),
(14, 'Sengketa Asuransi Syariah (Takaful)', 'Sengketa klaim atau pelaksanaan perjanjian asuransi berbasis syariah.', 1),
(15, 'Sengketa Ekonomi Syariah Lainnya', 'Sengketa akad/kontrak bisnis berdasarkan prinsip syariah.', 1),
(16, 'Perlawanan (Verzet)', 'Perlawanan atas putusan verstek maupun perlawanan pihak ketiga (derden verzet).', 1),
(17, 'Kewarisan', 'Otomatis diimpor dari API SIPP', 1);

-- =======================================================
-- MASTER USERS (users)
-- Default Password seluruh akun: password123 ($2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi)
-- =======================================================
INSERT INTO `users` (`id`, `nama`, `username`, `password`, `role`, `id_sipp`, `nip`, `is_active`) VALUES
(1, 'Rahmat Triadi, S.Kom.', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, '199510212020121004', 1),
(2, 'Muhammad Yusuf Putra, M.H., CPM', 'yusuf', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mediator', NULL, NULL, 1),
(3, 'Hasnia, S.H.I., M.H., CLA., CPM.', 'hasnia', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mediator', NULL, NULL, 1),
-- HAKIM
(4, 'Dra. Mukasipa, M.H.', '196610121992032002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'hakim', '38', '196610121992032002', 1),
(5, 'Drs. Satrio AM. Karim', '196601011993031011', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'hakim', '34', '196601011993031011', 1),
(6, 'Abdul Hakim, S.Ag., S.H., M.H.', '196807031992021001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'hakim', '42', '196807031992021001', 1),
(7, 'Dr. Mukhtaruddin Bahrum, S.H.I., M.H.I.', '197906132006041003', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'hakim', '40', '197906132006041003', 1),
(8, 'Muhamad Anwar Umar, S.Ag.', '197211052005021002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'hakim', '39', '197211052005021002', 1),
-- PANITERA PENGGANTI (PP)
(9, 'Muhiddin Litti, S.Ag., M.H.I.', '197109181998031003', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pp', '31', '197109181998031003', 1),
(10, 'Luthfiyah, S.Ag, M.H', '197106301998032001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pp', '20', '197106301998032001', 1),
(11, 'Haryono Daud, S.H.I.,M.H.', '197902072009121004', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pp', '44', '197902072009121004', 1),
(12, 'Alinda Ahmad Ishak, S.H.I., M.H.', '198010132009122001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pp', '45', '198010132009122001', 1),
(13, 'Mardiana Abubakar, S.H.I.,M.H.', '197811072009122002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pp', '42', '197811072009122002', 1),
(14, 'Djarnawi H. Datau, S.Ag.', '197107031998031006', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pp', '27', '197107031998031006', 1),
(15, 'Suratman Nang, S.H.', '196711271992021001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pp', '40', '196711271992021001', 1),
(16, 'Isma Katili, S.Ag.', '197806152006042013', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pp', '29', '197806152006042013', 1),
(17, 'Irsan Masri, S.H.I.', '196712021987031001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pp', '35', '196712021987031001', 1),
(18, 'Nizma Rizky Datau, S.H.I.', '198410062009042006', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pp', '30', '198410062009042006', 1),
(19, 'Rinda Wanni, S.H., M.H.', '198310102006042001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pp', '43', '198310102006042001', 1),
(20, 'Dorkas Eremst Yunginger, S.H.I., M.H.', '197307272006042001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pp', '48', '197307272006042001', 1),
(21, 'Misrawati Tululi, S.Ag., S.H.', '197209032003122002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pp', '47', '197209032003122002', 1),
(22, 'Nurhayati Hasan, S.H.I., M.H.', '197710101998032002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pp', '51', '197710101998032002', 1),
(23, 'Nurhayati Mustapa Hasan, S.H., M.H.', '198503282011012019', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pp', '50', '198503282011012019', 1),
(24, 'Nuryadin Akuba, S.H.I.', '198111242007041001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pp', '49', '198111242007041001', 1),
(25, 'Tamrin Yunus, S.Ag. M.H.', '197803252003121003', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pp', '46', '197803252003121003', 1);

-- =======================================================
-- MASTER MEDIATORS (mediators)
-- =======================================================
INSERT INTO `mediators` (`id`, `user_id`, `nama`, `jenis`, `no_sertifikat`, `id_sipp`, `email`, `no_hp`, `is_active`) VALUES
(1, 2, 'Muhammad Yusuf Putra, M.H., CPM', 'non_hakim', 'SERT-MED/NONHKM/001', NULL, 'yusuf@pa-gorontalo.go.id', '081234567890', 1),
(2, 3, 'Hasnia, S.H.I., M.H., CLA., CPM.', 'non_hakim', 'SERT-MED/NONHKM/002', NULL, 'hasnia@pa-gorontalo.go.id', '081234567891', 1),
(3, 4, 'Dra. Mukasipa, M.H.', 'hakim', 'SERTIF/HKM/38', '38', NULL, NULL, 1),
(4, 5, 'Drs. Satrio AM. Karim', 'hakim', 'SERTIF/HKM/34', '34', NULL, NULL, 1),
(5, 6, 'Abdul Hakim, S.Ag., S.H., M.H.', 'hakim', 'SERTIF/HKM/42', '42', NULL, NULL, 1),
(6, 7, 'Dr. Mukhtaruddin Bahrum, S.H.I., M.H.I.', 'hakim', 'SERTIF/HKM/40', '40', NULL, NULL, 1),
(7, 8, 'Muhamad Anwar Umar, S.Ag.', 'hakim', 'SERTIF/HKM/39', '39', NULL, NULL, 1);
