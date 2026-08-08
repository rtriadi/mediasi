-- Tambah kolom catatan_sesi di sesi_mediasi jika belum ada
ALTER TABLE sesi_mediasi ADD COLUMN IF NOT EXISTS catatan_sesi TEXT DEFAULT NULL COMMENT 'Catatan ringkas/jalannya sesi mediasi';

-- Tabel sesi_kehadiran untuk mencatat kehadiran per pihak per sesi
CREATE TABLE IF NOT EXISTS sesi_kehadiran (
  id INT(11) NOT NULL AUTO_INCREMENT,
  sesi_id INT(11) NOT NULL,
  pihak_id INT(11) NOT NULL,
  status_kehadiran ENUM('hadir', 'absen', 'kuasa') NOT NULL DEFAULT 'hadir',
  catatan VARCHAR(255) DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_sesi_pihak (sesi_id, pihak_id),
  CONSTRAINT fk_sk_sesi  FOREIGN KEY (sesi_id)  REFERENCES sesi_mediasi (id) ON DELETE CASCADE,
  CONSTRAINT fk_sk_pihak FOREIGN KEY (pihak_id) REFERENCES perkara_pihak (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Daftar presensi/kehadiran pihak pada sesi mediasi';
