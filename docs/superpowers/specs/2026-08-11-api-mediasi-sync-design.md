# Technical Specification: SIPP API Integration & Auto-Sync mediasi_db

**Date**: 2026-08-11  
**Project**: SIPO-MEDIASI PA Gorontalo  
**Author**: Antigravity AI & Master  

---

## 1. Executive Summary
SIPO-MEDIASI previously required Panitera Pengganti (PP) to manually input mediation case details. This specification defines the architecture, database schema migrations, API parser engine, and UI components needed to automate case creation by directly syncing data from the SIPP API into `mediasi_db`.

---

## 2. Architecture & Data Flow

```
[ External SIPP API ]
         │ (HTTP GET/POST JSON Response)
         ▼
[ SIPO-MEDIASI API Engine (SippApi Library) ]
   ├── Dynamic Settings (API URL, Token, Mediation Days)
   ├── Duplicate Check (nomor_perkara)
   ├── Entity Match / Auto-Insert (jenis_perkara, mediators, users)
   ├── Smart String Parser (penggugat, tergugat, kuasa, majelis_hakim)
   └── Auto-Calculator (tgl_batas_mediasi = tgl_penetapan_mediator + N days)
         │
         ▼
  [ mediasi_db ] ──► [ PP / Admin Dashboard & Real-time Notification ]
```

---

## 3. Database Schema Alterations (`mediasi_db`)

### 3.1 `perkara` Table Modifications
```sql
ALTER TABLE perkara 
  ADD COLUMN perkara_id_sipp VARCHAR(50) NULL AFTER id,
  ADD COLUMN majelis_hakim TEXT NULL AFTER jenis_perkara_id,
  ADD COLUMN majelis_id VARCHAR(100) NULL AFTER majelis_hakim,
  ADD COLUMN panitera_pengganti_id_sipp VARCHAR(50) NULL AFTER pp_id,
  ADD COLUMN panitera_sidang TEXT NULL AFTER panitera_pengganti_id_sipp,
  ADD COLUMN tgl_penetapan_mediator DATE NULL AFTER tgl_batas_mediasi;
```

### 3.2 `settings` Table Additions
```sql
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES 
('api_mediasi_url', 'http://192.168.100.5/perkara360/api/mediasi'),
('api_mediasi_key', ''),
('api_sync_auto', '1'),
('batas_waktu_mediasi_hari', '30'),
('api_last_sync', '');
```

---

## 4. API Engine & Data Mapping Specification

### 4.1 Input JSON Schema Example
```json
{
  "status": "success",
  "count": 1,
  "data": [
    {
      "nomor_perkara": "108/Pdt.G/2026/PA.Gtlo",
      "jenis_perkara": "Kewarisan",
      "majelis_hakim": "Hakim Ketua: Drs. Satrio AM. Karim</br>Hakim Anggota 1: Dra. Mukasipa, M.H.</br>Hakim Anggota 2: Muhamad Anwar Umar, S.Ag.",
      "majelis_id": "34,38,39",
      "panitera_pengganti_id": "44",
      "panitera_sidang": "Panitera Sidang: Haryono Daud, S.H.I.,M.H.",
      "tanggal_penetapan_mediator": "2026-04-02",
      "id_mediator": "44",
      "nama_mediator": "Muhammad Yusuf Putra",
      "status_mediator": "N",
      "email_mediator": "",
      "penggugat": "Nama: WIWIEK OCVIANTY...",
      "tergugat": "Nama: MUH. FAUZAN MUSTAPA...",
      "kuasa": "Nama: Ishak Suko., SH... | Nama: MUH. SYARIF..."
    }
  ]
}
```

### 4.2 Field Mapping Logic
1. **Perkara**:
   - `nomor_perkara` ──► `perkara.nomor_perkara` (Unique Key)
   - `jenis_perkara` ──► Matches `jenis_perkara.nama`. Auto-inserted into `jenis_perkara` if missing.
   - `majelis_hakim` ──► `perkara.majelis_hakim` (HTML text) and `perkara.nama_hakim` (Clean string summary).
   - `tanggal_penetapan_mediator` ──► `perkara.tgl_penetapan_mediator`
   - `tgl_batas_mediasi` ──► Calculated as `tanggal_penetapan_mediator + setting('batas_waktu_mediasi_hari') days`.

2. **Mediator**:
   - Check `mediators` where `nama = item.nama_mediator`.
   - If not found, insert into `mediators` (`nama`, `jenis` = `'hakim'` if status_mediator == `'H'` else `'non_hakim'`).
   - Create entry in `perkara_mediator` and record `perkara_mediator_log`.

3. **Party Parsing (`perkara_pihak`)**:
   - `penggugat`: Parse name, NIK, address, email. Insert `jenis = 'penggugat'`.
   - `tergugat`: Parse name, NIK, address, email. Insert `jenis = 'tergugat'`.
   - `kuasa`: Split by `|`. Attach attorney info to corresponding `perkara_pihak.kuasa_hukum`.

---

## 5. Implementation Strategy (Hybrid Sync)

1. **AJAX Button on Dashboard**:
   - Route: `/admin/api_sync/run` or `/pp/api_sync/run`
   - Triggers `SippApi->sync()` library method.
   - Returns summary JSON payload for toast UI alert.

2. **CLI Background Script**:
   - File: `cronjob_api_sync.php` or `php index.php api_sync/cron`
   - Executable via Windows Task Scheduler or CPanel Cronjob.

3. **Settings Management UI**:
   - Accessible in Admin Settings (`/admin/pengaturan`) to update API Endpoint URL and default mediation period days.

---

## 6. Verification Plan
- Test cURL API response parsing against mock JSON data.
- Verify DB insertion into `perkara`, `perkara_pihak`, `perkara_mediator`, `mediators`, and `jenis_perkara`.
- Verify duplicate prevention when re-running sync.
- Verify UI AJAX sync button and toast notifications on PP/Admin dashboards.
