-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 11, 2026 at 02:15 PM
-- Server version: 10.4.6-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mediasi_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

CREATE TABLE `ci_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `data` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ci_sessions`
--

INSERT INTO `ci_sessions` (`id`, `ip_address`, `timestamp`, `data`) VALUES
('fr1mvrvnssdb92kr7j739fp4nvgn4vpl', '::1', 1786450488, 0x5f5f63695f6c6173745f726567656e65726174657c693a313738363435303438383b757365725f69647c733a313a2231223b6e616d617c733a32313a2241646d696e6973747261746f72204d656469617369223b726f6c657c733a353a2261646d696e223b726f6c65737c613a313a7b693a303b733a353a2261646d696e223b7d69735f6d65646961746f727c623a303b6d65646961746f725f69647c4e3b69645f736970707c4e3b6e69707c4e3b),
('n21sq9jeslq8l726t6b5ht64ne5a3ap5', '::1', 1786450233, 0x5f5f63695f6c6173745f726567656e65726174657c693a313738363435303233323b),
('q063ll5fer9dcnh6ts6sqd9idcfb2rl2', '::1', 1786450488, 0x5f5f63695f6c6173745f726567656e65726174657c693a313738363435303438383b757365725f69647c733a313a2231223b6e616d617c733a32313a2241646d696e6973747261746f72204d656469617369223b726f6c657c733a353a2261646d696e223b726f6c65737c613a313a7b693a303b733a353a2261646d696e223b7d69735f6d65646961746f727c623a303b6d65646961746f725f69647c4e3b69645f736970707c4e3b6e69707c4e3b737563636573737c733a33343a2244617461206d65646961746f7220626572686173696c20646970657262617275692e223b5f5f63695f766172737c613a313a7b733a373a2273756363657373223b733a333a226f6c64223b7d),
('rh0knuao96n2fgia18rkjhn9khu0sp2l', '::1', 1786450233, 0x5f5f63695f6c6173745f726567656e65726174657c693a313738363435303233333b);

-- --------------------------------------------------------

--
-- Table structure for table `hasil_mediasi`
--

CREATE TABLE `hasil_mediasi` (
  `id` int(11) NOT NULL,
  `perkara_id` int(11) NOT NULL,
  `mediator_id` int(11) NOT NULL,
  `status_hasil` enum('berhasil_seluruhnya','berhasil_sebagian','tidak_berhasil','tidak_dapat_dilaksanakan') NOT NULL,
  `tgl_laporan` date DEFAULT NULL,
  `ringkasan_kesepakatan` text DEFAULT NULL,
  `alasan_kegagalan` text DEFAULT NULL,
  `file_laporan_pdf` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `jenis_perkara`
--

CREATE TABLE `jenis_perkara` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `jenis_perkara`
--

INSERT INTO `jenis_perkara` (`id`, `nama`, `keterangan`, `is_active`) VALUES
(1, 'Cerai Gugat', 'Gugatan cerai yang diajukan istri (penggugat) kepada suami (tergugat). Wajib menempuh mediasi berdasarkan PERMA No. 1 Tahun 2016. Mediasi bertujuan mengupayakan perdamaian / ishlah agar perkawinan dapat dipertahankan.', 1),
(2, 'Cerai Talak', 'Permohonan ikrar talak yang diajukan suami (pemohon) kepada istri (termohon). Wajib mediasi sesuai PERMA No. 1 Tahun 2016. Mediasi bertujuan memfasilitasi rujuk dan perdamaian sebelum sidang pokok perkara.', 1),
(3, 'Hak Asuh Anak (Hadhanah)', 'Sengketa hak pemeliharaan dan pengasuhan anak (hadhanah) pasca perceraian. Termasuk gugatan perubahan hak asuh. Wajib mediasi; kesepakatan dapat dituangkan dalam akta perdamaian (akta dading).', 1),
(4, 'Nafkah Iddah, Mut\'ah, dan Nafkah Anak', 'Sengketa kewajiban nafkah pasca perceraian, meliputi: nafkah selama masa iddah, mut\'ah (pemberian perpisahan kepada bekas istri), dan nafkah anak. Wajib mediasi sesuai PERMA No. 1 Tahun 2016.', 1),
(5, 'Harta Bersama (Gono-Gini)', 'Sengketa pembagian harta bersama (matrimonial property / harta gono-gini) dalam perkawinan, baik yang diajukan bersama perceraian maupun berdiri sendiri sebagai perkara terpisah. Wajib mediasi.', 1),
(6, 'Poligami', 'Permohonan izin beristri lebih dari satu (poligami). Jika ada pihak yang keberatan dan perkara bersifat kontensius, wajib menempuh proses mediasi sesuai PERMA No. 1 Tahun 2016.', 1),
(7, 'Pengesahan / Penetapan Pernikahan (Isbat Nikah)', 'Permohonan pengesahan pernikahan yang tidak tercatat secara resmi di Kantor Urusan Agama. Apabila ada pihak yang keberatan (kontensius), wajib ditempuh mediasi sebelum pemeriksaan pokok perkara.', 1),
(8, 'Kewarisan Islam', 'Sengketa pembagian harta warisan (tirkah) berdasarkan hukum Islam / Kompilasi Hukum Islam. Wajib mediasi; kesepakatan pembagian waris dapat dikuatkan melalui akta perdamaian yang berkekuatan hukum tetap.', 1),
(9, 'Hibah', 'Sengketa keabsahan atau pelaksanaan pemberian harta secara sukarela (hibah). Wajib mediasi sesuai PERMA No. 1 Tahun 2016. Mediasi bertujuan mencapai kesepakatan damai antar pihak.', 1),
(10, 'Wasiat', 'Sengketa pelaksanaan atau keabsahan wasiat (pesan akhir mengenai harta). Wajib mediasi sebagai sengketa perdata di lingkungan Pengadilan Agama.', 1),
(11, 'Wakaf', 'Sengketa peruntukan, pengelolaan, peralihan fungsi, atau keabsahan harta wakaf. Wajib mediasi sesuai PERMA No. 1 Tahun 2016; mediasi bertujuan menyelesaikan sengketa wakaf secara damai tanpa putusan pemaksa.', 1),
(12, 'Zakat / Infak / Sedekah', 'Sengketa pengelolaan atau penyaluran zakat, infak, dan sedekah (ZIS). Merupakan kompetensi absolut Pengadilan Agama berdasarkan UU No. 3 Tahun 2006. Wajib mediasi.', 1),
(13, 'Sengketa Perbankan Syariah', 'Sengketa antara nasabah dan bank / lembaga keuangan syariah atas akad-akad pembiayaan, seperti: murabahah, mudharabah, musyarakah, ijarah, istishna, salam. Merupakan kompetensi Pengadilan Agama sejak UU No. 3 Tahun 2006. Wajib mediasi.', 1),
(14, 'Sengketa Asuransi Syariah (Takaful)', 'Sengketa klaim atau pelaksanaan perjanjian asuransi berbasis syariah (takaful). Termasuk kompetensi Pengadilan Agama. Wajib mediasi sesuai PERMA No. 1 Tahun 2016.', 1),
(15, 'Sengketa Ekonomi Syariah Lainnya', 'Sengketa akad / kontrak bisnis berdasarkan prinsip syariah yang tidak masuk kategori di atas, termasuk: sukuk, reksa dana syariah, pegadaian syariah, koperasi syariah, dan lembaga keuangan mikro syariah. Wajib mediasi.', 1),
(16, 'Perlawanan (Verzet)', 'Perlawanan atas putusan verstek (verzet), perlawanan pihak berperkara (partij verzet), maupun perlawanan pihak ketiga (derden verzet) terhadap pelaksanaan putusan yang telah berkekuatan hukum tetap. Secara eksplisit diwajibkan mediasi oleh PERMA No. 1 Tahun 2016 Pasal 4 ayat (1) huruf d.', 1),
(17, 'Kewarisan', 'Otomatis diimpor dari API SIPP', 1),
(18, 'Penguasaan Anak', 'Otomatis diimpor dari API SIPP', 1),
(19, 'Izin Poligami', 'Otomatis diimpor dari API SIPP', 1),
(20, 'Lain-Lain', 'Otomatis diimpor dari API SIPP', 1),
(21, 'Harta Bersama', 'Otomatis diimpor dari API SIPP', 1);

-- --------------------------------------------------------

--
-- Table structure for table `log_notifikasi`
--

CREATE TABLE `log_notifikasi` (
  `id` int(11) NOT NULL,
  `perkara_id` int(11) DEFAULT NULL,
  `jenis` enum('email','wa') NOT NULL,
  `penerima` varchar(255) NOT NULL,
  `subjek` varchar(255) DEFAULT NULL,
  `pesan` text DEFAULT NULL,
  `status` enum('terkirim','gagal') NOT NULL DEFAULT 'gagal',
  `error_message` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `mediators`
--

CREATE TABLE `mediators` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `jenis` enum('hakim','non_hakim') NOT NULL,
  `no_sertifikat` varchar(100) DEFAULT NULL,
  `id_mediator` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `mediators`
--

INSERT INTO `mediators` (`id`, `user_id`, `nama`, `jenis`, `no_sertifikat`, `id_mediator`, `email`, `no_hp`, `is_active`) VALUES
(1, 2, 'Abdul Hakim, S.Ag., S.H., M.H.', 'hakim', 'SERTIF/HKM/42', '42', NULL, NULL, 1),
(2, 3, 'Dra. Mukasipa, M.H.', 'hakim', 'SERTIF/HKM/38', '36', NULL, NULL, 1),
(3, 4, 'Drs. Satrio AM. Karim', 'hakim', 'SERTIF/HKM/34', '28', NULL, NULL, 1),
(4, 5, 'Dr. Mukhtaruddin Bahrum, S.H.I., M.H.I.', 'hakim', 'SERTIF/HKM/40', '41', NULL, NULL, 1),
(5, 6, 'Muhamad Anwar Umar, S.Ag.', 'hakim', 'SERTIF/HKM/39', '38', NULL, NULL, 1),
(6, 7, 'Muhammad Yusuf Putra, M.H., CPM', 'non_hakim', '127/KMA/SK/VIII/2020', '44', 'rtriadi01@gmail.com', '082271021336', 1),
(7, 8, 'Hasnia, S.H.I., M.H., CLA., CPM.', 'non_hakim', '089/KMA/SK/VI/2021', '43', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `perkara`
--

CREATE TABLE `perkara` (
  `id` int(11) NOT NULL,
  `perkara_id_sipp` varchar(50) DEFAULT NULL,
  `nomor_perkara` varchar(50) NOT NULL,
  `jenis_perkara_id` int(11) NOT NULL,
  `majelis_hakim` text DEFAULT NULL,
  `majelis_id` varchar(255) DEFAULT NULL,
  `panitera_sidang` varchar(500) DEFAULT NULL,
  `tgl_batas_mediasi` date NOT NULL,
  `status` enum('menunggu','proses','selesai') DEFAULT 'menunggu',
  `pp_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `perkara_kuasa`
--

CREATE TABLE `perkara_kuasa` (
  `id` int(11) NOT NULL,
  `perkara_id` int(11) NOT NULL,
  `pihak_id` int(11) DEFAULT NULL,
  `nama` varchar(150) NOT NULL,
  `nik` varchar(30) DEFAULT NULL,
  `ttl` varchar(100) DEFAULT NULL,
  `pekerjaan` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `perkara_mediator`
--

CREATE TABLE `perkara_mediator` (
  `id` int(11) NOT NULL,
  `perkara_id` int(11) NOT NULL,
  `mediator_id` int(11) NOT NULL,
  `tgl_penetapan` date DEFAULT NULL,
  `status_mediator` varchar(10) DEFAULT 'N',
  `is_active` tinyint(1) DEFAULT 1,
  `alasan_penggantian` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `perkara_mediator_log`
--

CREATE TABLE `perkara_mediator_log` (
  `id` int(11) NOT NULL,
  `perkara_id` int(11) NOT NULL,
  `mediator_id` int(11) NOT NULL,
  `assigned_by` int(11) NOT NULL,
  `tgl_assign` datetime NOT NULL DEFAULT current_timestamp(),
  `tgl_diganti` datetime DEFAULT NULL COMMENT 'Kapan mediator ini digantikan. NULL = masih aktif',
  `diganti_oleh` int(11) DEFAULT NULL COMMENT 'User ID PP/Admin yang mengganti'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Riwayat penugasan mediator per perkara';

-- --------------------------------------------------------

--
-- Table structure for table `perkara_pihak`
--

CREATE TABLE `perkara_pihak` (
  `id` int(11) NOT NULL,
  `perkara_id` int(11) NOT NULL,
  `jenis_pihak` enum('penggugat','tergugat','pemohon','termohon','turut_tergugat') NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nik` varchar(50) DEFAULT NULL,
  `ttl` varchar(100) DEFAULT NULL,
  `pekerjaan` varchar(255) DEFAULT NULL,
  `pendidikan` varchar(255) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `urutan` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `ruangan`
--

CREATE TABLE `ruangan` (
  `id` int(11) NOT NULL,
  `nama_ruangan` varchar(100) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ruangan`
--

INSERT INTO `ruangan` (`id`, `nama_ruangan`, `keterangan`, `is_active`) VALUES
(1, 'Ruang Mediasi Pengadilan Agama Gorontalo', 'Lantai 1 Gedung Utama Pengadilan Agama Gorontalo', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sesi_kehadiran`
--

CREATE TABLE `sesi_kehadiran` (
  `id` int(11) NOT NULL,
  `sesi_id` int(11) NOT NULL,
  `pihak_id` int(11) DEFAULT NULL,
  `kuasa_id` int(11) DEFAULT NULL,
  `status_kehadiran` enum('hadir','absen','kuasa') NOT NULL DEFAULT 'hadir',
  `catatan` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Daftar presensi/kehadiran pihak pada sesi mediasi';

-- --------------------------------------------------------

--
-- Table structure for table `sesi_mediasi`
--

CREATE TABLE `sesi_mediasi` (
  `id` int(11) NOT NULL,
  `perkara_id` int(11) NOT NULL,
  `mediator_id` int(11) NOT NULL,
  `tgl_mediasi` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `ruangan_id` int(11) DEFAULT NULL,
  `tempat_lain` varchar(200) DEFAULT NULL,
  `link_virtual` varchar(500) DEFAULT NULL COMMENT 'URL link virtual meeting',
  `platform_virtual` varchar(50) DEFAULT NULL COMMENT 'Zoom / Google Meet / MS Teams dll',
  `keterangan` text DEFAULT NULL,
  `status_sesi` enum('terjadwal','selesai','batal','dijadwal_ulang') NOT NULL DEFAULT 'terjadwal',
  `alasan_reschedule` text DEFAULT NULL COMMENT 'Alasan reschedule atau pembatalan',
  `created_at` datetime DEFAULT current_timestamp(),
  `catatan_sesi` text DEFAULT NULL COMMENT 'Catatan ringkas/jalannya sesi mediasi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'nama_aplikasi', 'SIPO-MEDIASI', '2026-08-02 10:57:36'),
(2, 'slogan_aplikasi', 'Sistem Informasi Pengelolaan Mediasi Perkara', '2026-08-02 10:57:36'),
(3, 'nama_satker', 'PA Gorontalo', '2026-08-02 10:59:41'),
(4, 'logo_aplikasi', 'logo_1785668354.png', '2026-08-02 10:59:14'),
(5, 'wa_notif_active', '0', '2026-08-02 11:08:28'),
(6, 'wa_api_token', '', '2026-08-02 11:08:28'),
(7, 'wa_api_url', 'https://api.fonnte.com/send', '2026-08-02 11:08:28'),
(8, 'email_notif_active', '1', '2026-08-11 00:45:08'),
(9, 'smtp_host', 'smtp.gmail.com', '2026-08-02 11:18:15'),
(10, 'smtp_port', '587', '2026-08-02 11:18:15'),
(11, 'smtp_user', 'surat@pa-gorontalo.go.id', '2026-08-03 12:54:36'),
(12, 'smtp_pass', 'fbnd xuga dejt xllk', '2026-08-03 12:54:36'),
(13, 'smtp_crypto', 'tls', '2026-08-02 11:18:15'),
(14, 'mail_from_name', 'SIPO-MEDIASI PA Gorontalo', '2026-08-02 11:18:15'),
(15, 'api_mediasi_url', 'http://165.99.98.225:8781/perkara360/api/mediasi', '2026-08-11 10:38:46'),
(16, 'api_mediasi_key', 'perkara360_api_key_5d8f2b1a9c4e7f3b', '2026-08-11 03:19:06'),
(17, 'api_sync_auto', '1', '2026-08-11 03:11:47'),
(18, 'batas_waktu_mediasi_hari', '30', '2026-08-11 03:11:47'),
(19, 'api_last_sync', '', '2026-08-11 11:59:29'),
(20, 'api_sync_interval_menit', '15', '2026-08-11 03:20:15');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(100) NOT NULL DEFAULT 'pp',
  `id_sipp` varchar(50) DEFAULT NULL,
  `nip` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `role`, `id_sipp`, `nip`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Administrator Mediasi', 'admin', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'admin', NULL, NULL, 1, '2026-08-11 20:11:00', '2026-08-11 20:11:00'),
(2, 'Abdul Hakim, S.Ag., S.H., M.H.', '196807031992021001', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'hakim', '42', '196807031992021001', 1, '2026-08-11 20:11:00', '2026-08-11 20:11:00'),
(3, 'Dra. Mukasipa, M.H.', '196610121992032002', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'hakim', '38', '196610121992032002', 1, '2026-08-11 20:11:00', '2026-08-11 20:11:00'),
(4, 'Drs. Satrio AM. Karim', '196601011993031011', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'hakim', '34', '196601011993031011', 1, '2026-08-11 20:11:00', '2026-08-11 20:11:00'),
(5, 'Dr. Mukhtaruddin Bahrum, S.H.I., M.H.I.', '197906132006041003', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'hakim', '40', '197906132006041003', 1, '2026-08-11 20:11:00', '2026-08-11 20:11:00'),
(6, 'Muhamad Anwar Umar, S.Ag.', '197211052005021002', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'hakim', '39', '197211052005021002', 1, '2026-08-11 20:11:00', '2026-08-11 20:11:00'),
(7, 'Muhammad Yusuf Putra, M.H., CPM', 'mediator_yusuf', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'mediator', NULL, NULL, 1, '2026-08-11 20:11:00', '2026-08-11 20:11:48'),
(8, 'Hasnia, S.H.I., M.H., CLA., CPM.', 'mediator_hasnia', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'mediator', NULL, NULL, 1, '2026-08-11 20:11:00', '2026-08-11 20:11:56'),
(9, 'Rinda Wanni, S.H., M.H.', '198310102006042001', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'pp', '43', '198310102006042001', 1, '2026-08-11 20:11:00', '2026-08-11 20:11:00'),
(10, 'Muhiddin Litti, S.Ag., M.H.I.', '197109181998031003', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'pp', '31', '197109181998031003', 1, '2026-08-11 20:11:00', '2026-08-11 20:11:00'),
(11, 'Luthfiyah, S.Ag, M.H', '197106301998032001', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'pp', '20', '197106301998032001', 1, '2026-08-11 20:11:00', '2026-08-11 20:11:00'),
(12, 'Haryono Daud, S.H.I., M.H.', '197902072009121004', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'pp', '44', '197902072009121004', 1, '2026-08-11 20:11:00', '2026-08-11 20:11:00'),
(13, 'Alinda Ahmad Ishak, S.H.I., M.H.', '198010132009122001', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'pp', '45', '198010132009122001', 1, '2026-08-11 20:11:00', '2026-08-11 20:11:00'),
(14, 'Mardiana Abubakar, S.H.I., M.H.', '197811072009122002', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'pp', '42', '197811072009122002', 1, '2026-08-11 20:11:00', '2026-08-11 20:11:00'),
(15, 'Djarnawi H. Datau, S.Ag.', '197107031998031006', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'pp', '27', '197107031998031006', 1, '2026-08-11 20:11:00', '2026-08-11 20:11:00'),
(16, 'Suratman Nang, S.H.', '196711271992021001', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'pp', '40', '196711271992021001', 1, '2026-08-11 20:11:00', '2026-08-11 20:11:00'),
(17, 'Isma Katili, S.Ag.', '197806152006042013', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'pp', '29', '197806152006042013', 1, '2026-08-11 20:11:00', '2026-08-11 20:11:00'),
(18, 'Irsan Masri, S.H.I.', '196712021987031001', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'pp', '35', '196712021987031001', 1, '2026-08-11 20:11:00', '2026-08-11 20:11:00'),
(19, 'Nizma Rizky Datau, S.H.I.', '198410062009042006', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'pp', '30', '198410062009042006', 1, '2026-08-11 20:11:00', '2026-08-11 20:11:00'),
(20, 'Dorkas Eremst Yunginger, S.H.I., M.H.', '197307272006042001', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'pp', '48', '197307272006042001', 1, '2026-08-11 20:11:00', '2026-08-11 20:11:00'),
(21, 'Misrawati Tululi, S.Ag., S.H.', '197209032003122002', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'pp', '47', '197209032003122002', 1, '2026-08-11 20:11:00', '2026-08-11 20:11:00'),
(22, 'Nurhayati Hasan, S.H.I., M.H.', '197710101998032002', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'pp', '51', '197710101998032002', 1, '2026-08-11 20:11:00', '2026-08-11 20:11:00'),
(23, 'Nurhayati Mustapa Hasan, S.H., M.H.', '198503282011012019', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'pp', '50', '198503282011012019', 1, '2026-08-11 20:11:00', '2026-08-11 20:11:00'),
(24, 'Nuryadin Akuba, S.H.I.', '198111242007041001', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'pp', '49', '198111242007041001', 1, '2026-08-11 20:11:00', '2026-08-11 20:11:00'),
(25, 'Tamrin Yunus, S.Ag. M.H.', '197803252003121003', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'pp', '46', '197803252003121003', 1, '2026-08-11 20:11:00', '2026-08-11 20:11:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ci_sessions`
--
ALTER TABLE `ci_sessions`
  ADD PRIMARY KEY (`id`,`ip_address`);

--
-- Indexes for table `hasil_mediasi`
--
ALTER TABLE `hasil_mediasi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `perkara_id` (`perkara_id`),
  ADD KEY `mediator_id` (`mediator_id`);

--
-- Indexes for table `jenis_perkara`
--
ALTER TABLE `jenis_perkara`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log_notifikasi`
--
ALTER TABLE `log_notifikasi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mediators`
--
ALTER TABLE `mediators`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `perkara`
--
ALTER TABLE `perkara`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nomor_perkara` (`nomor_perkara`),
  ADD UNIQUE KEY `uk_nomor_perkara` (`nomor_perkara`),
  ADD KEY `jenis_perkara_id` (`jenis_perkara_id`),
  ADD KEY `pp_id` (`pp_id`);

--
-- Indexes for table `perkara_kuasa`
--
ALTER TABLE `perkara_kuasa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_kuasa_perkara` (`perkara_id`),
  ADD KEY `fk_kuasa_pihak` (`pihak_id`);

--
-- Indexes for table `perkara_mediator`
--
ALTER TABLE `perkara_mediator`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pm_perkara` (`perkara_id`),
  ADD KEY `fk_pm_mediator` (`mediator_id`);

--
-- Indexes for table `perkara_mediator_log`
--
ALTER TABLE `perkara_mediator_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_log_perkara` (`perkara_id`),
  ADD KEY `idx_log_mediator` (`mediator_id`),
  ADD KEY `pml_assigned_fk` (`assigned_by`),
  ADD KEY `pml_diganti_fk` (`diganti_oleh`);

--
-- Indexes for table `perkara_pihak`
--
ALTER TABLE `perkara_pihak`
  ADD PRIMARY KEY (`id`),
  ADD KEY `perkara_id` (`perkara_id`);

--
-- Indexes for table `ruangan`
--
ALTER TABLE `ruangan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sesi_kehadiran`
--
ALTER TABLE `sesi_kehadiran`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_sesi_pihak` (`sesi_id`,`pihak_id`),
  ADD KEY `fk_sk_pihak` (`pihak_id`);

--
-- Indexes for table `sesi_mediasi`
--
ALTER TABLE `sesi_mediasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mediator_id` (`mediator_id`),
  ADD KEY `ruangan_id` (`ruangan_id`),
  ADD KEY `idx_sesi_status` (`status_sesi`),
  ADD KEY `idx_sesi_tgl` (`tgl_mediasi`),
  ADD KEY `idx_sesi_perkara` (`perkara_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hasil_mediasi`
--
ALTER TABLE `hasil_mediasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jenis_perkara`
--
ALTER TABLE `jenis_perkara`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `log_notifikasi`
--
ALTER TABLE `log_notifikasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mediators`
--
ALTER TABLE `mediators`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `perkara`
--
ALTER TABLE `perkara`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `perkara_kuasa`
--
ALTER TABLE `perkara_kuasa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `perkara_mediator`
--
ALTER TABLE `perkara_mediator`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `perkara_mediator_log`
--
ALTER TABLE `perkara_mediator_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `perkara_pihak`
--
ALTER TABLE `perkara_pihak`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ruangan`
--
ALTER TABLE `ruangan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sesi_kehadiran`
--
ALTER TABLE `sesi_kehadiran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sesi_mediasi`
--
ALTER TABLE `sesi_mediasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `hasil_mediasi`
--
ALTER TABLE `hasil_mediasi`
  ADD CONSTRAINT `hasil_mediasi_ibfk_1` FOREIGN KEY (`perkara_id`) REFERENCES `perkara` (`id`),
  ADD CONSTRAINT `hasil_mediasi_ibfk_2` FOREIGN KEY (`mediator_id`) REFERENCES `mediators` (`id`);

--
-- Constraints for table `mediators`
--
ALTER TABLE `mediators`
  ADD CONSTRAINT `mediators_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `perkara`
--
ALTER TABLE `perkara`
  ADD CONSTRAINT `perkara_ibfk_1` FOREIGN KEY (`jenis_perkara_id`) REFERENCES `jenis_perkara` (`id`),
  ADD CONSTRAINT `perkara_ibfk_2` FOREIGN KEY (`pp_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `perkara_kuasa`
--
ALTER TABLE `perkara_kuasa`
  ADD CONSTRAINT `fk_kuasa_perkara` FOREIGN KEY (`perkara_id`) REFERENCES `perkara` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kuasa_pihak` FOREIGN KEY (`pihak_id`) REFERENCES `perkara_pihak` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `perkara_mediator`
--
ALTER TABLE `perkara_mediator`
  ADD CONSTRAINT `fk_pm_mediator` FOREIGN KEY (`mediator_id`) REFERENCES `mediators` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pm_perkara` FOREIGN KEY (`perkara_id`) REFERENCES `perkara` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `perkara_mediator_log`
--
ALTER TABLE `perkara_mediator_log`
  ADD CONSTRAINT `pml_assigned_fk` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pml_diganti_fk` FOREIGN KEY (`diganti_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pml_mediator_fk` FOREIGN KEY (`mediator_id`) REFERENCES `mediators` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pml_perkara_fk` FOREIGN KEY (`perkara_id`) REFERENCES `perkara` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `perkara_pihak`
--
ALTER TABLE `perkara_pihak`
  ADD CONSTRAINT `perkara_pihak_ibfk_1` FOREIGN KEY (`perkara_id`) REFERENCES `perkara` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sesi_kehadiran`
--
ALTER TABLE `sesi_kehadiran`
  ADD CONSTRAINT `fk_sk_pihak` FOREIGN KEY (`pihak_id`) REFERENCES `perkara_pihak` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sk_sesi` FOREIGN KEY (`sesi_id`) REFERENCES `sesi_mediasi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sesi_mediasi`
--
ALTER TABLE `sesi_mediasi`
  ADD CONSTRAINT `sesi_mediasi_ibfk_1` FOREIGN KEY (`perkara_id`) REFERENCES `perkara` (`id`),
  ADD CONSTRAINT `sesi_mediasi_ibfk_2` FOREIGN KEY (`mediator_id`) REFERENCES `mediators` (`id`),
  ADD CONSTRAINT `sesi_mediasi_ibfk_3` FOREIGN KEY (`ruangan_id`) REFERENCES `ruangan` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
