-- =======================================================
-- SIPO-MEDIASI PA GORONTALO
-- Migration 004: Clean 3NF Schema Refactoring
-- File: docs/sql/004_refactor_schema_3nf.sql
-- =======================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Buat Tabel Kuasa Hukum Terpisah jika belum ada
CREATE TABLE IF NOT EXISTS `perkara_kuasa` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `perkara_id` INT(11) NOT NULL,
  `pihak_id` INT(11) DEFAULT NULL,
  `nama` VARCHAR(150) NOT NULL,
  `nik` VARCHAR(30) DEFAULT NULL,
  `ttl` VARCHAR(100) DEFAULT NULL,
  `pekerjaan` VARCHAR(100) DEFAULT NULL,
  `alamat` TEXT DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `no_hp` VARCHAR(20) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_kuasa_perkara` (`perkara_id`),
  KEY `fk_kuasa_pihak` (`pihak_id`),
  CONSTRAINT `fk_kuasa_perkara` FOREIGN KEY (`perkara_id`) REFERENCES `perkara` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_kuasa_pihak` FOREIGN KEY (`pihak_id`) REFERENCES `perkara_pihak` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Bersihkan Kolom Redundan dari tabel perkara
-- (Drop tanggal_penetapan_mediator, panitera_pengganti_id, jenis_perkara string, nama_hakim)
SET @exist_tanggal_penetapan = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'perkara' AND COLUMN_NAME = 'tanggal_penetapan_mediator');
SET @exist_panitera_pengganti_id = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'perkara' AND COLUMN_NAME = 'panitera_pengganti_id');
SET @exist_jenis_perkara_str = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'perkara' AND COLUMN_NAME = 'jenis_perkara');
SET @exist_nama_hakim = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'perkara' AND COLUMN_NAME = 'nama_hakim');

ALTER TABLE `perkara`
  DROP COLUMN IF EXISTS `tanggal_penetapan_mediator`,
  DROP COLUMN IF EXISTS `panitera_pengganti_id`,
  DROP COLUMN IF EXISTS `jenis_perkara`,
  DROP COLUMN IF EXISTS `nama_hakim`;

-- 3. Pastikan kolom-kolom standar 3NF berada di tabel perkara
ALTER TABLE `perkara`
  ADD COLUMN IF NOT EXISTS `perkara_id_sipp` VARCHAR(50) NULL AFTER `id`,
  ADD COLUMN IF NOT EXISTS `majelis_hakim` TEXT NULL AFTER `jenis_perkara_id`,
  ADD COLUMN IF NOT EXISTS `majelis_id` VARCHAR(100) NULL AFTER `majelis_hakim`,
  ADD COLUMN IF NOT EXISTS `pp_id` INT(11) NULL AFTER `majelis_id`,
  ADD COLUMN IF NOT EXISTS `panitera_pengganti_id_sipp` VARCHAR(50) NULL AFTER `pp_id`,
  ADD COLUMN IF NOT EXISTS `panitera_sidang` VARCHAR(255) NULL AFTER `panitera_pengganti_id_sipp`,
  ADD COLUMN IF NOT EXISTS `tgl_penetapan_mediator` DATE NULL AFTER `panitera_sidang`,
  ADD COLUMN IF NOT EXISTS `tgl_batas_mediasi` DATE NULL AFTER `tgl_penetapan_mediator`;

-- 4. Tambah kolom kuasa_id di sesi_kehadiran jika belum ada
ALTER TABLE `sesi_kehadiran`
  ADD COLUMN IF NOT EXISTS `kuasa_id` INT(11) NULL AFTER `pihak_id`,
  MODIFY COLUMN `pihak_id` INT(11) NULL;

SET FOREIGN_KEY_CHECKS = 1;
