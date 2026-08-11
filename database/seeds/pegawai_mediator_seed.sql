-- SQL Seed Script for Mediasi DB (Users & Mediators)
-- Default password for all users: 'password' (bcrypt: $2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6)

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Reset and Insert Clean Users Data (1 Admin, Hakim, Mediator, PP)
DELETE FROM `users`;
ALTER TABLE `users` AUTO_INCREMENT = 1;

INSERT INTO `users` (`id`, `username`, `password`, `nama`, `role`, `id_sipp`, `nip`, `is_active`) VALUES
(1, 'admin', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'Administrator Mediasi', 'admin', NULL, NULL, 1),
(2, '196807031992021001', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'Abdul Hakim, S.Ag., S.H., M.H.', 'hakim', '42', '196807031992021001', 1),
(3, '196610121992032002', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'Dra. Mukasipa, M.H.', 'hakim', '38', '196610121992032002', 1),
(4, '196601011993031011', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'Drs. Satrio AM. Karim', 'hakim', '34', '196601011993031011', 1),
(5, '197906132006041003', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'Dr. Mukhtaruddin Bahrum, S.H.I., M.H.I.', 'hakim', '40', '197906132006041003', 1),
(6, '197211052005021002', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'Muhamad Anwar Umar, S.Ag.', 'hakim', '39', '197211052005021002', 1),
(7, 'mediator_yusuf', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'Muhammad Yusuf Putra, M.H., CPM', 'mediator', '44', NULL, 1),
(8, 'mediator_hasnia', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'Hasnia, S.H.I., M.H., CLA., CPM.', 'mediator', '43', NULL, 1),
(9, '198310102006042001', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'Rinda Wanni, S.H., M.H.', 'pp', '43', '198310102006042001', 1),
(10, '197109181998031003', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'Muhiddin Litti, S.Ag., M.H.I.', 'pp', '31', '197109181998031003', 1),
(11, '197106301998032001', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'Luthfiyah, S.Ag, M.H', 'pp', '20', '197106301998032001', 1),
(12, '197902072009121004', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'Haryono Daud, S.H.I., M.H.', 'pp', '44', '197902072009121004', 1),
(13, '198010132009122001', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'Alinda Ahmad Ishak, S.H.I., M.H.', 'pp', '45', '198010132009122001', 1),
(14, '197811072009122002', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'Mardiana Abubakar, S.H.I., M.H.', 'pp', '42', '197811072009122002', 1),
(15, '197107031998031006', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'Djarnawi H. Datau, S.Ag.', 'pp', '27', '197107031998031006', 1),
(16, '196711271992021001', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'Suratman Nang, S.H.', 'pp', '40', '196711271992021001', 1),
(17, '197806152006042013', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'Isma Katili, S.Ag.', 'pp', '29', '197806152006042013', 1),
(18, '196712021987031001', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'Irsan Masri, S.H.I.', 'pp', '35', '196712021987031001', 1),
(19, '198410062009042006', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'Nizma Rizky Datau, S.H.I.', 'pp', '30', '198410062009042006', 1),
(20, '197307272006042001', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'Dorkas Eremst Yunginger, S.H.I., M.H.', 'pp', '48', '197307272006042001', 1),
(21, '197209032003122002', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'Misrawati Tululi, S.Ag., S.H.', 'pp', '47', '197209032003122002', 1),
(22, '197710101998032002', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'Nurhayati Hasan, S.H.I., M.H.', 'pp', '51', '197710101998032002', 1),
(23, '198503282011012019', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'Nurhayati Mustapa Hasan, S.H., M.H.', 'pp', '50', '198503282011012019', 1),
(24, '198111242007041001', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'Nuryadin Akuba, S.H.I.', 'pp', '49', '198111242007041001', 1),
(25, '197803252003121003', '$2y$10$gNC6p3EIHu6jF7ulkRQaEe4E5tuqKNusgm7HibS65gU1IpThIG9e6', 'Tamrin Yunus, S.Ag. M.H.', 'pp', '46', '197803252003121003', 1);

-- 2. Reset and Insert Clean Mediators Data (Hakim & Non-Hakim)
TRUNCATE TABLE `mediators`;

INSERT INTO `mediators` (`id`, `user_id`, `id_mediator`, `nama`, `jenis`, `no_sertifikat`, `is_active`) VALUES
(1, 2, '42', 'Abdul Hakim, S.Ag., S.H., M.H.', 'hakim', 'SERTIF/HKM/42', 1),
(2, 3, '38', 'Dra. Mukasipa, M.H.', 'hakim', 'SERTIF/HKM/38', 1),
(3, 4, '34', 'Drs. Satrio AM. Karim', 'hakim', 'SERTIF/HKM/34', 1),
(4, 5, '40', 'Dr. Mukhtaruddin Bahrum, S.H.I., M.H.I.', 'hakim', 'SERTIF/HKM/40', 1),
(5, 6, '39', 'Muhamad Anwar Umar, S.Ag.', 'hakim', 'SERTIF/HKM/39', 1),
(6, 7, '44', 'Muhammad Yusuf Putra, M.H., CPM', 'non_hakim', '127/KMA/SK/VIII/2020', 1),
(7, 8, '43', 'Hasnia, S.H.I., M.H., CLA., CPM.', 'non_hakim', '089/KMA/SK/VI/2021', 1);

SET FOREIGN_KEY_CHECKS = 1;
