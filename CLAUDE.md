# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Sistem Presensi Hybrid (GPS + Foto Selfie) — skripsi Universitas Mulawarman, Teknik Elektro.
Laravel 12 / PHP 8.2 / MySQL (XAMPP) / port 8000.

## Common Commands

```bash
# Development
php artisan serve                        # start server (port 8000)
npm run dev                              # compile assets (Vite + Tailwind)
npm run build                            # production build

# Database
php artisan migrate                      # run new migrations
php artisan migrate:fresh --seed         # reset + reseed
php artisan optimize:clear               # clear all caches
php artisan storage:link                 # link storage → public (run after fresh setup)

# Testing
php artisan test                         # run all tests
php artisan test --filter TestName       # run single test
./vendor/bin/pest                        # run Pest directly

# Code quality
./vendor/bin/pint                        # format code (Laravel Pint)
```

## Architecture

### Frontend Stack

- Blade templating + Tailwind CSS (utility-first)
- Alpine.js for lightweight interactivity
- Leaflet.js + OpenStreetMap for geofencing maps (loaded via CDN in `admin/layouts/app.blade.php`)
- SweetAlert2 for modals/alerts (CDN)
- jQuery (CDN, used in some views)
- Vite compiles `resources/css/app.css` + `resources/js/app.js`

### Admin Layout

All admin pages extend `<x-app-layout>` which maps to `app/View/Components/AppLayout.php` → `resources/views/admin/layouts/app.blade.php`. This layout includes the sidebar, top navbar, Leaflet, Alpine, and SweetAlert2 CDN scripts.

### Controllers

Admin CRUD lives in `app/Http/Controllers/Admin/`. The root `app/Http/Controllers/` has auth-adjacent controllers (ProfileController, DashboardController, PresensiController) and legacy stubs (now deleted — active replacements are under `Admin/`).

### Data Model & Relationships

The central chain for the attendance session (sesi presensi):

```
SesiPresensi
  ↳ belongsTo JadwalPerkuliahan (via jadwal_perkuliahan_id)
       ↳ belongsTo KelasPerkuliahan (via kelas_perkuliahan_id)
            ↳ belongsTo MataKuliah
            ↳ belongsTo Dosen
            ↳ belongsTo TahunAjaran
       ↳ belongsTo Ruangan          ← GPS center + radius_meter
  ↳ hasMany Presensi (mahasiswa attendance records)
       ↳ belongsTo Mahasiswa
```

**Correct access patterns:**

- Ruangan: `$sesi->jadwalPerkuliahan->ruangan` (SesiPresensi has `jadwal_perkuliahan_id`)
- Mata kuliah from sesi: `$sesi->jadwalPerkuliahan->kelasPerkuliahan->mataKuliah`
- `radius_meter` column (not `radius`)

**Known bug in `resources/views/admin/sesi/live.blade.php` line 37**: uses the old path `$sesi->kelasPerkuliahan->ruangan` which will fail; should be `$sesi->jadwalPerkuliahan->ruangan`.

### Import System

Dosen, Mahasiswa, MataKuliah, Kelas support Excel import via `Maatwebsite\Excel` (see `app/Imports/`). Import classes use `WithHeadingRow` — Excel column headers must match exactly (e.g. `mata_kuliah`, `dosen`, `tahun_ajaran`, `nama_kelas`).

### File Storage

- Selfie photos: `storage/app/public/presensi/selfie/`
- Profile photos: `storage/app/public/profil/`
- Always use `Storage::disk('public')` — never base64 in DB.

### API (Planned — Not Yet Built)

- Future Flutter app will consume `routes/api.php` endpoints
- Laravel Sanctum auth for all API routes
- Face Recognition via external Python API (HTTP client integration)
- Response format must be: `{"status": true, "message": "...", "data": {...}}`

## Critical Rules

**GPS / Geofencing:**

- `latitude`/`longitude` are stored as `varchar` → always `parseFloat()` in JS before passing to Leaflet
- `radius_meter` is integer (meters) — use Haversine formula for distance check
- GPS validation must happen server-side, not only in Flutter client
- Never hardcode coordinates in code — read from `ruangans` table

**Eloquent:**

- Always eager load to avoid N+1: `with(['relation', 'relation.nested'])`
- No queries inside Blade `@foreach` loops

**Code conventions:**

- camelCase for PHP methods and variables
- snake_case for DB column names
- Domain variables may use Bahasa Indonesia: `$sesi`, `$ruangan`, `$mahasiswa`, `$dosen`, `$kelas`
- Form Request classes for validation (not inline `validate()` in controllers)
- Extract to Service class when a method exceeds ~30 lines

## Status

**Done:** Admin CRUD for Dosen, Mahasiswa, Mata Kuliah, Ruangan, Tahun Ajaran, Kelas Perkuliahan, Jadwal Perkuliahan, Sesi Presensi (buka/tutup + live radar view), Excel imports, DomPDF reports.

**In progress:** Live radar geofencing display (bug on line 37 of `admin/sesi/live.blade.php`), Presensi mahasiswa flow.

**Not started:** API endpoints (routes/api.php), Flutter app, Face Recognition integration.
