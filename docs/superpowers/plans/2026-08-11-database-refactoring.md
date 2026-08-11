# Database Architecture Refactoring & Clean 3NF Implementation Plan

> **For agentic workers:** REQUIRED: Use superpowers:subagent-driven-development (if subagents available) or superpowers:executing-plans to implement this plan. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Refactor the database schema to clean 3NF, separate `perkara_pihak` and `perkara_kuasa`, eliminate all redundant columns in `perkara`, and update `SippApi.php` parser and models to support the new schema.

**Architecture:** Execute SQL migration `004_refactor_schema_3nf.sql`, update `SippApi.php` cURL parser for multi-attorney parsing into `perkara_kuasa`, and update models `M_perkara.php` and `M_mediator.php`.

**Tech Stack:** PHP 7.4/8.x, CodeIgniter 3, MySQL 5.7/8.0.

---

## Chunk 1: Database Migration & Schema Setup

### Task 1: Execute SQL Migration `004_refactor_schema_3nf.sql`

**Files:**
- Create: `docs/sql/004_refactor_schema_3nf.sql`

- [ ] **Step 1: Execute 004_refactor_schema_3nf.sql against `mediasi_db`**

Run: `C:\xampp\mysql\bin\mysql.exe -u root mediasi_db < docs/sql/004_refactor_schema_3nf.sql`
Expected: Success with no errors.

- [ ] **Step 2: Verify `perkara_kuasa` table structure**

Run: `C:\xampp\mysql\bin\mysql.exe -u root mediasi_db -e "DESCRIBE perkara_kuasa; DESCRIBE perkara;"`
Expected: `perkara_kuasa` present, redundant columns removed from `perkara`.

- [ ] **Step 3: Commit**

```bash
git add docs/sql/004_refactor_schema_3nf.sql
git commit -m "feat: apply 004_refactor_schema_3nf database migration"
```

---

## Chunk 2: SippApi Library & Model Refactoring

### Task 2: Refactor `SippApi.php` Parser for `perkara_pihak` and `perkara_kuasa`

**Files:**
- Modify: `application/libraries/SippApi.php`
- Modify: `application/models/M_perkara.php`

- [ ] **Step 1: Update `SippApi.php` data_perkara insertion array**

Ensure `data_perkara` inserts only non-redundant columns:
`perkara_id_sipp`, `nomor_perkara`, `jenis_perkara_id`, `majelis_hakim`, `majelis_id`, `pp_id`, `panitera_pengganti_id_sipp`, `panitera_sidang`, `tgl_penetapan_mediator`, `tgl_batas_mediasi`.

- [ ] **Step 2: Update `SippApi.php` `parse_and_insert_pihak` to handle multi-attorney in `perkara_kuasa`**

Parse `penggugat` and `tergugat` into `perkara_pihak`.
Split `kuasa` string by `|` delimiter and parse each attorney into `perkara_kuasa`.

- [ ] **Step 3: Test syntax and run manual sync test**

Run: `C:\xampp\php\php.exe -l application\libraries\SippApi.php`
Expected: `No syntax errors detected`

- [ ] **Step 4: Commit**

```bash
git add application/libraries/SippApi.php application/models/M_perkara.php
git commit -m "feat: update SippApi parser for clean 3NF schema and perkara_kuasa"
```
