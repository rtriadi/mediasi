# ⚖️ SIPO-MEDIASI (Sistem Informasi Pengelolaan Mediasi Perkara)

**SIPO-MEDIASI** adalah aplikasi manajemen mediasi perkara perdata di Pengadilan Agama yang dibangun khusus untuk mengotomasikan alur registrasi perkara, penjadwalan sesi mediasi, notifikasi otomatis kepada para pihak, hingga rekapitulasi laporan statistik keberhasilan mediasi sesuai standar **Mahkamah Agung RI (PERMA No. 1 Tahun 2016)** dan **Direktorat Jenderal Badan Peradilan Agama (Badilag)**.

---

## 📋 Spesifikasi Sistem & Teknologi (*Tech Stack*)

### **Core Backend & Database:**
- **Framework:** CodeIgniter 3.1.13 (Kompatibel dengan PHP 7.4 hingga PHP 8.2+)
- **Database Engine:** MySQL / MariaDB (`InnoDB`)
- **Fitur PHP 8.2+ Compatibility:** Menggunakan `#[\AllowDynamicProperties]` pada Base Controller.

### **Frontend & UI/UX Design System:**
- **CSS Framework:** Tailwind CSS (via CDN / Utility Classes)
- **Typography:** Google Fonts (*Inter* & *Outfit*)
- **Icon Pack:** FontAwesome 6 Pro
- **Interactive Calendar:** FullCalendar v6 (Responsive Block Cards)
- **Data Visualization:** Chart.js 4 (Donut, Bar, & Line Charts Analytics)
- **Form Component:** Select2 (Searchable Select Dropdown)

### **External Integration & Gateways:**
- **WhatsApp Gateway:** Fonnte REST API (Direct WA Notification Broadcast)
- **Email Gateway:** Native PHP Mailer / SMTP (Custom Host, Port, TLS/SSL)
- **Failover System:** Silent Failover & Error Logging di Database (`log_notifikasi`) dengan tombol **🔄 Kirim Ulang**.

---

## 🌟 Fitur-Fitur Utama Aplikasi

### 1. 👥 Multi-Role Access Control (6 Hak Akses User):
- **Admin System:** Akses penuh manajemen user, mediator, ruangan, jenis perkara, pengaturan aplikasi, log notifikasi, dan backup database.
- **Panitera Pengganti (PP):** Registrasi data perkara baru, pengisian data para pihak (Penggugat/Tergugat), dan penetapan mediator.
- **Mediator (Hakim & Non-Hakim):** Pembuatan jadwal mediasi (Sesi 1-N), input link virtual meeting (Zoom/Meet), dan pelaporan Hasil Mediasi (Berhasil / Berhasil Sebagian / Tidak Berhasil).
- **Pimpinan PA (Ketua / Wakil Ketua):** Access Dashboard Executive Analitik, Grafik Keberhasilan, serta Ekspor Laporan Rekapitulasi (PDF/Excel).
- **Hakim / Majelis Hakim:** Memantau perkembangan mediasi perkara yang ditangani.
- **Portal Publik (Masyarakat):** Halaman informasi publik tanpa login untuk mengecek status & jadwal mediasi perkara secara transparan.

### 2. 📅 Penjadwalan & Kalender Interaktif:
- Kalender interaktif visual berbasis blok waktu.
- Penjadwalan mediasi tatap muka (pilihan ruangan) atau online (virtual meeting link).
- Fitur **Penjadwalan Ulang (*Reschedule*)** dan **Pembatalan Sesi** yang otomatis memperbarui tampilan kalender.
- **Sequential Session Enforcement:** Mencegah pembuat jadwal sesi baru jika sesi mediasi sebelumnya yang berstatus `terjadwal` belum diselesaikan.
- **Presensi Kehadiran & Catatan Sesi:** Mediator dapat mencatat kehadiran setiap pihak (*Hadir*, *Absen*, atau *Kuasa Hukum*) beserta catatan resume jalannya sesi sebelum mengubah status sesi menjadi `selesai`.

### 3. 📲 Otomatisasi Broadcast Notifikasi (WA & Email):
- Notifikasi penugasan baru dan pemberitahuan penggantian mediator langsung dikirim ke WhatsApp dan Email mediator terkait.
- Notifikasi jadwal mediasi terisi langsung dikirim ke WhatsApp dan Email para pihak.
- **Smart Filtering:** Pihak yang tidak memiliki Email/No HP terisi otomatis diabaikan tanpa menyebabkan error.
- **Silent Failover:** Jika koneksi internet/gateway terputus, sistem menyimpan log error secara diam-diam dan menyediakan fitur **Kirim Ulang** 1-Klik di panel Admin.

### 4. 🔄 Penggantian Mediator & Jejak Audit (Audit Trail):
- **Tabel Log Riwayat Penugasan (`perkara_mediator_log`):** Mencatat secara otomatis setiap riwayat penetapan dan penggantian mediator.
- **Automatic Active Session Takeover (Ide 1):** Sesi mediasi berstatus `terjadwal` yang dibuat mediator lama otomatis dialihkan kepemilikannya ke mediator baru saat terjadi penggantian mediator oleh PP, sehingga mediator baru dapat langsung melanjutkan dan mencatat presensi kehadiran.
- **Dual Notification:** Mediator lama otomatis menerima notifikasi pencabutan penugasan via WA/Email saat diganti, dan mediator baru menerima notifikasi penugasan.
- **Timeline Riwayat Mediator:** Menampilkan riwayat lengkap mediator (aktif/digantikan) di halaman detail perkara PP, Mediator, dan Hakim.
- **Proteksi Perkara Selesai:** Mencegah penggantian mediator jika perkara sudah memiliki Hasil Mediasi Final / Selesai.
- **Custom Modern Error Views:** Seluruh pesan kesalahan sistem (`show_error`, `show_404`, database error) kini ditangani dengan antarmuka modern yang interaktif, dilengkapi tombol navigasi kembali dan dashboard.

### 5. 📑 Seed Data Jenis Perkara (PERMA No. 1 Tahun 2016):
- Dilengkapi dengan 16 seed data jenis perkara perdata Islam wajib mediasi (Perceraian, Hadhanah, Harta Bersama, Kewarisan, Ekonomi Syariah, Wakaf, Zakat, hingga Perlawanan/Verzet).

### 6. 📊 Laporan Rekapitulasi & Visual Analitik:
- Rumus kalkulasi keberhasilan mediasi sesuai standar Badilag MA:
  $$\text{Persentase Keberhasilan (\%)} = \frac{\text{Berhasil (Damai)} + \text{Berhasil Sebagian}}{\text{Total Mediasi Selesai}} \times 100\%$$
- **Ekspor Excel (.xls):** Unduh berkas laporan rekapitulasi bulanan/tahunan bersih.
- **Cetak PDF Resmi:** Format cetak dokumen ber-Kop Pengadilan Agama dan kolom tanda tangan pimpinan/panitera.

### 7. 🛡️ Keamanan, Paginasi & Alat Administrator:
- **Custom Pagination Builder:** Paginasi terpusat berbasis query string (`?page=N`) yang aman, presisi, dan dilengkapi tampilan informasi halaman & total data.
- **Clean URL:** Penghilangan `index.php` dari URL menggunakan `.htaccess` dan mod_rewrite.
- **1-Click DB Backup:** Fitur unduh cadangan database `.sql.zip` langsung dari GUI Admin.
- **Reset Password Default:** Admin dapat me-reset password user yang lupa ke default (`123456`).
- **Web Shell Protection:** Folder `uploads/` dilindungi dari eksekusi skrip PHP.

---

## 💻 Persyaratan Server (*System Requirements*)

- **Web Server:** Apache / Nginx / LiteSpeed (dengan modul `mod_rewrite` aktif)
- **PHP Version:** PHP >= 7.4 (Disarankan PHP 8.1 atau PHP 8.2)
- **Database:** MySQL >= 5.7 atau MariaDB >= 10.3
- **Ekstensi PHP Wajib:** `mysqli`, `curl`, `json`, `mbstring`, `zip`, `gd`

---

## 🚀 Panduan Instalasi (*Installation Guide*)

### Langkah 1: Download / Clone Proyek
Salin seluruh folder proyek ini ke direktori web server Anda:
- **XAMPP (Windows):** `C:\xampp\htdocs\mediasi`
- **Linux / Production:** `/var/www/html/mediasi`

### Langkah 2: Import Database
1. Buka **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Buat database baru dengan nama `db_mediasi`.
3. Import berkas `db_mediasi.sql` (atau jalankan skrip migrasi database) yang tersedia di folder proyek.

### Langkah 3: Konfigurasi Database & Base URL
Buka berkas `application/config/database.php` dan sesuaikan kredensial MySQL Anda:
```php
'hostname' => 'localhost',
'username' => 'root',        // Isikan username database Anda
'password' => '',            // Isikan password database Anda
'database' => 'db_mediasi',  // Nama database
```

Buka berkas `application/config/config.php` dan pastikan base_url sesuai:
```php
$config['base_url']   = 'http://localhost/mediasi/';
$config['index_page'] = ''; // Biarkan kosong (mod_rewrite aktif)
```

---

## 🔑 Kredensial Akun Bawaan (*Default Login*)

Anda dapat mencoba aplikasi menggunakan akun bawaan berikut (Password default: `123456`):

| Role / Hak Akses | Username | Password Default | Akses Utama |
|---|---|---|---|
| **Admin System** | `admin` | `123456` | Kelola User, Settings, Log, Backup |
| **Panitera Pengganti** | `pp1` | `123456` | Input Perkara & Penetapan Mediator |
| **Mediator** | `mediator1` | `123456` | Penjadwalan & Laporan Mediasi |
| **Pimpinan PA** | `pimpinan` | `123456` | Dashboard Statistik & Ekspor Laporan |

---

## 📖 Manual Penggunaan Aplikasi (*User Guide*)

### 1. Alur Kerja Panitera Pengganti (PP):
1. Login sebagai PP (`pp1`).
2. Masuk ke menu **Daftar Perkara** ➔ Klik **Tambah Perkara Baru**.
3. Isi Nomor Perkara, Jenis Perkara, Nama Hakim, serta Data Pihak 1 (Penggugat) dan Pihak 2 (Tergugat).
4. Pilih Mediator yang ditunjuk, lalu simpan. Perkara akan berstatus **Menunggu Sesi 1**.

### 2. Alur Kerja Mediator:
1. Login sebagai Mediator (`mediator1`).
2. Masuk ke menu **Perkara Mediasi Saya** ➔ Pilih perkara yang perlu dijadwalkan.
3. Klik **Buat Jadwal Mediasi**:
   - Tentukan Tanggal, Jam Sesi, Ruangan, atau Link Meeting Virtual.
   - Sistem akan otomatis mengirim pemberitahuan WA/Email ke para pihak.
4. Setelah mediasi selesai dilaksanakan, klik **Lapor Hasil Mediasi**:
   - Pilih Hasil: *Berhasil*, *Berhasil Sebagian*, atau *Tidak Berhasil*.
   - Masukkan Tanggal Laporan & Ringkasan Kesepakatan.

### 3. Alur Kerja Pimpinan & Admin:
1. Login sebagai Pimpinan (`pimpinan`) atau Admin (`admin`).
2. Buka **Dashboard Analitik** untuk melihat grafik rasio keberhasilan bulanan & kinerja per mediator.
3. Masuk ke menu **Laporan Mediasi** ➔ Filter berdasarkan Bulan/Triwulan/Tahun ➔ Klik **Export Excel** atau **Export PDF / Cetak**.

---

## ⚙️ Pengaturan Keamanan Shared Hosting (*Deployment Guide*)

Sebelum mengunggah proyek ke **Shared Hosting (cPanel / Live Server)**:
1. Edit berkas `index.php` di root folder, ubah mode environment ke `production`:
   ```php
   define('ENVIRONMENT', 'production');
   ```
2. Pastikan file `.htaccess` berada di root direktori agar URL bersih tanpa `index.php` berjalan sempurna.
3. Masuk ke menu **Pengaturan Aplikasi** di Admin GUI untuk mengisi Token Fonnte WhatsApp & Server SMTP Email resmi institusi.
4. Sangat disarankan untuk langsung mengganti password seluruh akun bawaan setelah instalasi pertama.

---

**SIPO-MEDIASI — Pengadilan Agama Gorontalo**  
*Mewujudkan Peradilan yang Sederhana, Cepat, Biaya Ringan, dan Transparan.* ⚖️✨
