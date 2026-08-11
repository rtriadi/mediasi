# SIPP API Integration & mediasi_db Auto-Sync Implementation Plan

> **For agentic workers:** REQUIRED: Use superpowers:subagent-driven-development (if subagents available) or superpowers:executing-plans to implement this plan. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Automate case ingestion from SIPP API into `mediasi_db`, auto-mapping Panitera Pengganti (PP), Majelis Hakim, and Mediator data, with hybrid triggers (manual AJAX dashboard button & background cronjob).

**Architecture:** Extend database schema for `perkara` and `settings`, create a `SippApi` library service in CodeIgniter 3 for cURL request, string parsing, duplicate check, and DB auto-insert, expose sync endpoints in Admin/PP controllers, and update UI dashboard views.

**Tech Stack:** PHP 7.4/8.x (CodeIgniter 3), MySQL/MariaDB, cURL, JavaScript (Fetch API), Tailwind CSS.

---

## File Structure & Plan Decomposition

- **Database Migration**: `docs/sql/002_alter_perkara_add_api_fields.sql` (Creates migration SQL for new columns & settings)
- **Library Service**: `application/libraries/SippApi.php` (Central logic for API fetching, parsing, auto-insertion)
- **Controllers**: 
  - `application/controllers/Admin/Api_sync.php` (Admin sync endpoint & config update)
  - `application/controllers/Pp/Api_sync.php` (PP sync endpoint)
- **Cronjob Script**: `cronjob_api_sync.php` (CLI script for automated background sync)
- **Views**:
  - `application/views/admin/dashboard/index.php` (Adds Sync API widget)
  - `application/views/pp/dashboard/index.php` (Adds Sync API widget)
  - `application/views/admin/pengaturan/index.php` (Adds SIPP API settings form)

---

## Chunk 1: Database Migration & Core Library Service

### Task 1: Database Migration Script
**Files:**
- Create: `docs/sql/002_alter_perkara_add_api_fields.sql`

- [ ] **Step 1: Create SQL migration file**

```sql
-- Migration: Add SIPP API fields to perkara table and settings entries
USE mediasi_db;

ALTER TABLE perkara 
  ADD COLUMN perkara_id_sipp VARCHAR(50) NULL AFTER id,
  ADD COLUMN majelis_hakim TEXT NULL AFTER jenis_perkara_id,
  ADD COLUMN majelis_id VARCHAR(100) NULL AFTER majelis_hakim,
  ADD COLUMN panitera_pengganti_id_sipp VARCHAR(50) NULL AFTER pp_id,
  ADD COLUMN panitera_sidang TEXT NULL AFTER panitera_pengganti_id_sipp,
  ADD COLUMN tgl_penetapan_mediator DATE NULL AFTER tgl_batas_mediasi;

INSERT IGNORE INTO settings (setting_key, setting_value) VALUES 
('api_mediasi_url', 'http://192.168.100.5/perkara360/api/mediasi'),
('api_mediasi_key', ''),
('api_sync_auto', '1'),
('batas_waktu_mediasi_hari', '30'),
('api_last_sync', '');
```

- [ ] **Step 2: Execute migration against `mediasi_db`**
Run: `mysql -u root mediasi_db < docs/sql/002_alter_perkara_add_api_fields.sql`
Expected: Query OK

- [ ] **Step 3: Commit migration file**
```bash
git add -f docs/sql/002_alter_perkara_add_api_fields.sql
git commit -m "db: add migration script for SIPP API fields and settings"
```

---

### Task 2: `SippApi` Library Service
**Files:**
- Create: `application/libraries/SippApi.php`

- [ ] **Step 1: Create `SippApi.php` library class**
Implement methods:
- `fetch_api_data()`: Uses cURL to call `api_mediasi_url` from `settings`.
- `sync()`: Parses JSON response, checks `count > 0`, loops data items:
  - Duplicate check by `nomor_perkara`.
  - Auto-match / Auto-insert `jenis_perkara`.
  - Auto-match / Auto-insert `mediators` & `perkara_mediator`.
  - Calculate `tgl_batas_mediasi` based on `tanggal_penetapan_mediator + batas_waktu_mediasi_hari`.
  - Parse `penggugat`, `tergugat`, and `kuasa` strings into `perkara_pihak`.
  - Record sync summary results (`inserted`, `updated`, `skipped`, `failed`).

- [ ] **Step 2: Verify `SippApi.php` syntax**
Run: `php -l application/libraries/SippApi.php`
Expected: No syntax errors detected

- [ ] **Step 3: Commit Library**
```bash
git add application/libraries/SippApi.php
git commit -m "feat: add SippApi library service for API fetching and parsing"
```

---

## Chunk 2: Controllers & Background Cronjob Script

### Task 3: Admin & PP Sync Controllers
**Files:**
- Create: `application/controllers/Admin/Api_sync.php`
- Modify: `application/controllers/Pp/Monitor.php` or Create `application/controllers/Pp/Api_sync.php`

- [ ] **Step 1: Create `Admin/Api_sync.php` controller**
Expose `run()` action returning JSON response for frontend AJAX requests.

- [ ] **Step 2: Create `Pp/Api_sync.php` controller**
Expose `run()` action for PP role.

- [ ] **Step 3: Test endpoints via CLI / php script**
Run: `php index.php admin/api_sync/run`
Expected: JSON output `{ "status": "success", ... }`

- [ ] **Step 4: Commit Controllers**
```bash
git add application/controllers/Admin/Api_sync.php application/controllers/Pp/Api_sync.php
git commit -m "feat: add API sync controller endpoints for Admin and PP"
```

---

### Task 4: CLI Background Cronjob Script
**Files:**
- Create: `cronjob_api_sync.php`

- [ ] **Step 1: Create `cronjob_api_sync.php` standalone CLI script**
Loads CI instance, calls `$this->sippapi->sync()`, logs sync activity to `application/logs/`.

- [ ] **Step 2: Run CLI cronjob test**
Run: `php cronjob_api_sync.php`
Expected: Logs sync execution success.

- [ ] **Step 3: Commit Cronjob script**
```bash
git add cronjob_api_sync.php
git commit -m "feat: add background CLI cronjob script for SIPP API sync"
```

---

## Chunk 3: Dashboard UI Widgets & Verification

### Task 5: Dashboard UI Widgets
**Files:**
- Modify: `application/views/admin/dashboard/index.php`
- Modify: `application/views/pp/dashboard/index.php`
- Modify: `application/views/admin/pengaturan/index.php`

- [ ] **Step 1: Add SIPP Sync Status Widget & Button to Admin Dashboard**
- [ ] **Step 2: Add SIPP Sync Status Widget & Button to PP Dashboard**
- [ ] **Step 3: Add SIPP API URL & Duration Config Fields to Admin Settings**
- [ ] **Step 4: Commit UI Changes**
```bash
git add application/views/admin/dashboard/index.php application/views/pp/dashboard/index.php application/views/admin/pengaturan/index.php
git commit -m "feat: add SIPP API sync widgets and settings UI"
```

---

### Task 6: End-to-End Verification
- [ ] **Step 1: Execute manual sync via CLI**
- [ ] **Step 2: Verify `perkara` and `perkara_pihak` tables populated correctly**
- [ ] **Step 3: Commit final updates**
