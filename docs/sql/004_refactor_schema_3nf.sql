# Design Spec: Database Architecture Refactoring & Clean 3NF Schema (`mediasi_db`)

**Date**: 2026-08-11  
**Status**: APPROVED by Master  
**Target System**: SIPO-MEDIASI PA Gorontalo (`mediasi_db`)

---

## 1. Executive Summary & Goals

Proyek ini bertujuan melakukan **refactoring total dan normalisasi 3NF** pada struktur database `mediasi_db` agar memiliki arsitektur yang rapi, profesional, bebas dari kolom ganda/redundan, dan mampu menangani seluruh siklus mediasi perkara dari impor SIPP API hingga laporan hasil akhir mediasi.

---

## 2. Key Architecture Improvements

1. **Pencegahan Kolom Ganda/Redundan pada Tabel `perkara`**:
   - Menghapus `tanggal_penetapan_mediator` (duplicate), menyisakan `tgl_penetapan_mediator`.
   - Menghapus `panitera_pengganti_id` (duplicate), menyisakan `pp_id` (FK ke `users.id`) dan `panitera_pengganti_id_sipp`.
   - Menghapus string `jenis_perkara`, mengandalkan FK `jenis_perkara_id` ke tabel `jenis_perkara`.
   - Menghapus `nama_hakim` (duplicate), mengandalkan `majelis_hakim` dan `majelis_id`.

2. **Pemisahan Pihak & Kuasa Hukum (`perkara_pihak` & `perkara_kuasa`)**:
   - Data Penggugat dan Tergugat dari API SIPP diparse dan disimpan secara terstruktur di tabel `perkara_pihak`.
   - Data **Kuasa Hukum (Pengacara)** yang dari API SIPP seringkali berjumlah lebih dari satu individu diparse dan disimpan di tabel tersendiri **`perkara_kuasa`** lengkap dengan NIK, Email, dan Nomor HP per individu pengacara.

3. **Manajemen Penugasan Mediator & Histori Penggantian (`perkara_mediator`)**:
   - Catatan penugasan mediator tersimpan relational. Jika terjadi penggantian mediator di tengah jalan, record lama diberi flag `is_active = 0` dan diisi `alasan_penggantian`.

4. **Trilogi Sesi, Kehadiran, & Hasil Mediasi**:
   - `sesi_mediasi`: Mengelola perjumpaan/sesi mediasi, jam mulai/selesai, ruangan, dan catatan mediator.
   - `sesi_kehadiran`: Mengatur presensi kehadiran Penggugat, Tergugat, maupun Kuasa Hukum per sesi.
   - `hasil_mediasi`: Menyimpan laporan hasil akhir mediasi (`berhasil_seluruhnya`, `berhasil_sebagian`, `tidak_berhasil`, `tidak_dapat_dilaksanakan`), ringkasan kesepakatan/alasan kegagalan, dan lampiran PDF Akta Dading/Laporan.

---

## 3. Detailed Table Schema Definitions

### 3.1 `users`
```sql
CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nama` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(100) NOT NULL DEFAULT 'pp',
  `id_sipp` VARCHAR(50) DEFAULT NULL,
  `nip` VARCHAR(50) DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 3.2 `mediators`
```sql
CREATE TABLE `mediators` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) DEFAULT NULL,
  `nama` VARCHAR(100) NOT NULL,
  `jenis` ENUM('hakim','non_hakim') NOT NULL,
  `no_sertifikat` VARCHAR(100) DEFAULT NULL,
  `id_sipp` VARCHAR(50) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `no_hp` VARCHAR(20) DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_mediators_user` (`user_id`),
  CONSTRAINT `fk_mediators_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 3.3 `jenis_perkara`
```sql
CREATE TABLE `jenis_perkara` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nama` VARCHAR(150) NOT NULL UNIQUE,
  `keterangan` TEXT DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 3.4 `ruangan`
```sql
CREATE TABLE `ruangan` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_ruangan` VARCHAR(100) NOT NULL,
  `keterangan` TEXT DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 3.5 `settings`
```sql
CREATE TABLE `settings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT DEFAULT NULL,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 3.6 `perkara`
```sql
CREATE TABLE `perkara` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `perkara_id_sipp` VARCHAR(50) DEFAULT NULL,
  `nomor_perkara` VARCHAR(100) NOT NULL UNIQUE,
  `jenis_perkara_id` INT(11) NOT NULL,
  `majelis_hakim` TEXT DEFAULT NULL,
  `majelis_id` VARCHAR(100) DEFAULT NULL,
  `pp_id` INT(11) DEFAULT NULL,
  `panitera_pengganti_id_sipp` VARCHAR(50) DEFAULT NULL,
  `panitera_sidang` VARCHAR(255) DEFAULT NULL,
  `tgl_penetapan_mediator` DATE DEFAULT NULL,
  `tgl_batas_mediasi` DATE DEFAULT NULL,
  `status` ENUM('menunggu','proses','berhasil_sebagian','berhasil_seluruhnya','tidak_berhasil','tidak_dapat_dilaksanakan') DEFAULT 'menunggu',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_perkara_jenis` (`jenis_perkara_id`),
  KEY `fk_perkara_pp` (`pp_id`),
  CONSTRAINT `fk_perkara_jenis` FOREIGN KEY (`jenis_perkara_id`) REFERENCES `jenis_perkara` (`id`),
  CONSTRAINT `fk_perkara_pp` FOREIGN KEY (`pp_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 3.7 `perkara_pihak`
```sql
CREATE TABLE `perkara_pihak` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `perkara_id` INT(11) NOT NULL,
  `jenis_pihak` ENUM('penggugat','tergugat','pemohon','termohon') NOT NULL,
  `nama` VARCHAR(150) NOT NULL,
  `nik` VARCHAR(30) DEFAULT NULL,
  `ttl` VARCHAR(100) DEFAULT NULL,
  `pekerjaan` VARCHAR(100) DEFAULT NULL,
  `pendidikan` VARCHAR(50) DEFAULT NULL,
  `alamat` TEXT DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `no_hp` VARCHAR(20) DEFAULT NULL,
  `urutan` INT(11) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_pihak_perkara` (`perkara_id`),
  CONSTRAINT `fk_pihak_perkara` FOREIGN KEY (`perkara_id`) REFERENCES `perkara` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 3.8 `perkara_kuasa`
```sql
CREATE TABLE `perkara_kuasa` (
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
```

### 3.9 `perkara_mediator`
```sql
CREATE TABLE `perkara_mediator` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `perkara_id` INT(11) NOT NULL,
  `mediator_id` INT(11) NOT NULL,
  `tgl_penetapan` DATE DEFAULT NULL,
  `status_mediator` VARCHAR(20) DEFAULT 'N',
  `is_active` TINYINT(1) DEFAULT 1,
  `alasan_penggantian` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_pm_perkara` (`perkara_id`),
  KEY `fk_pm_mediator` (`mediator_id`),
  CONSTRAINT `fk_pm_perkara` FOREIGN KEY (`perkara_id`) REFERENCES `perkara` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pm_mediator` FOREIGN KEY (`mediator_id`) REFERENCES `mediators` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 3.10 `sesi_mediasi`
```sql
CREATE TABLE `sesi_mediasi` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `perkara_id` INT(11) NOT NULL,
  `mediator_id` INT(11) NOT NULL,
  `ruangan_id` INT(11) DEFAULT NULL,
  `sesi_ke` INT(11) NOT NULL DEFAULT 1,
  `tanggal_sesi` DATE NOT NULL,
  `jam_mulai` TIME NOT NULL,
  `jam_selesai` TIME NOT NULL,
  `agenda` TEXT DEFAULT NULL,
  `catatan_mediator` TEXT DEFAULT NULL,
  `status_sesi` ENUM('terjadwal','selesai','ditunda','batal') DEFAULT 'terjadwal',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_sm_perkara` (`perkara_id`),
  KEY `fk_sm_mediator` (`mediator_id`),
  KEY `fk_sm_ruangan` (`ruangan_id`),
  CONSTRAINT `fk_sm_perkara` FOREIGN KEY (`perkara_id`) REFERENCES `perkara` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sm_mediator` FOREIGN KEY (`mediator_id`) REFERENCES `mediators` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sm_ruangan` FOREIGN KEY (`ruangan_id`) REFERENCES `ruangan` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 3.11 `sesi_kehadiran`
```sql
CREATE TABLE `sesi_kehadiran` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `sesi_mediasi_id` INT(11) NOT NULL,
  `pihak_id` INT(11) DEFAULT NULL,
  `kuasa_id` INT(11) DEFAULT NULL,
  `kehadiran` ENUM('hadir','tidak_hadir','diwakili_kuasa') DEFAULT 'tidak_hadir',
  `keterangan` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_sk_sesi` (`sesi_mediasi_id`),
  KEY `fk_sk_pihak` (`pihak_id`),
  KEY `fk_sk_kuasa` (`kuasa_id`),
  CONSTRAINT `fk_sk_sesi` FOREIGN KEY (`sesi_mediasi_id`) REFERENCES `sesi_mediasi` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sk_pihak` FOREIGN KEY (`pihak_id`) REFERENCES `perkara_pihak` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sk_kuasa` FOREIGN KEY (`kuasa_id`) REFERENCES `perkara_kuasa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 3.12 `hasil_mediasi`
```sql
CREATE TABLE `hasil_mediasi` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `perkara_id` INT(11) NOT NULL UNIQUE,
  `mediator_id` INT(11) NOT NULL,
  `tgl_laporan` DATE NOT NULL,
  `status_hasil` ENUM('berhasil_seluruhnya','berhasil_sebagian','tidak_berhasil','tidak_dapat_dilaksanakan') NOT NULL,
  `ringkasan_kesepakatan` TEXT DEFAULT NULL,
  `alasan_kegagalan` TEXT DEFAULT NULL,
  `file_laporan_pdf` VARCHAR(255) DEFAULT NULL,
  `file_dading_pdf` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_hm_perkara` (`perkara_id`),
  KEY `fk_hm_mediator` (`mediator_id`),
  CONSTRAINT `fk_hm_perkara` FOREIGN KEY (`perkara_id`) REFERENCES `perkara` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_hm_mediator` FOREIGN KEY (`mediator_id`) REFERENCES `mediators` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 4. Parser API SIPP Refactoring Specification (`SippApi.php`)

Library `SippApi.php` akan diperbarui untuk melakukan parsing string kompleks dari SIPP API ke tabel-tabel terpisah:

1. **`penggugat` string parsing**:
   - Extract `Nama`, `NIK`, `TTL`, `Pekerjaan`, `Pendidikan`, `Alamat`, `Email`.
   - Insert ke tabel `perkara_pihak` (`jenis_pihak = 'penggugat'`).
2. **`tergugat` string parsing**:
   - Extract `Nama`, `NIK`, `TTL`, `Pekerjaan`, `Pendidikan`, `Alamat`, `Email`.
   - Insert ke tabel `perkara_pihak` (`jenis_pihak = 'tergugat'`).
3. **`kuasa` string parsing** (Dua tingkat pemisahan: dipisah karakter `|` untuk tiap pengacara):
   - Extract `Nama`, `NIK`, `TTL`, `Pekerjaan`, `Pendidikan`, `Alamat`, `Email`.
   - Insert tiap individu pengacara ke tabel **`perkara_kuasa`** dengan merujuk ke `perkara_id` dan `pihak_id`.
