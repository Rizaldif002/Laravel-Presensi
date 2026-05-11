# ROADMAP — Sistem Presensi Hybrid FT UNMUL

> Terakhir diperbarui: 11 Mei 2026
> Dibuat berdasarkan analisis kode aktual — bukan asumsi.
> Jika ROADMAP ini bertentangan dengan CLAUDE.md, percayai ROADMAP ini karena dibuat dari inspeksi file nyata.

---

## Estimasi Progres Keseluruhan

```
Backend Laravel  : [████████░░]  85%
Frontend Web     : [█████████░]  92%
API Endpoint     : [████████░░]  80%
Flutter Mobile   : [░░░░░░░░░░]   0%
Testing & Docs   : [░░░░░░░░░░]   0%

TOTAL PROGRES    : [█████░░░░░]  51%
```

> Catatan: Flutter (0%) menekan total secara signifikan.
> Bobot: Backend 35% · Frontend 20% · API 15% · Flutter 20% · Testing 10%

---

## Status Per Modul

### Backend Laravel

#### Auth & Middleware

- [x] Login Admin & Dosen via email/NIP (Session)
- [x] Login Mahasiswa via NIM (API Sanctum token)
- [x] RoleMiddleware — redirect per role (`role:admin`, `role:dosen`)
- [x] Logout web & API
- [x] Profile edit (nama, email, password, foto profil)

#### Master Data Admin

- [x] **Ruangan + Geofencing** — index ✅ | store ✅ | update ✅ | destroy ✅
- [x] **Mata Kuliah** — index ✅ | store ✅ | update ✅ | destroy ✅ | import Excel ✅
- [x] **Dosen** — index ✅ | store ✅ | update ✅ | destroy ✅ | import Excel ✅
- [x] **Mahasiswa** — index ✅ | store ✅ | update ✅ | destroy ✅ | import Excel ✅
- [x] **Tahun Ajaran** — index ✅ | store ✅ | update ✅ | destroy ✅ | setAktif ✅

#### Akademik

- [x] **Kelas Perkuliahan** — index ✅ | store ✅ | update ✅ | destroy ✅ | import Excel ✅
- [x] **Jadwal Perkuliahan** — index ✅ | store ✅ | update ✅ | destroy ✅
- [x] **Peserta Kelas** — index ✅ | store (manual) ✅ | destroy ✅ | import Excel ✅

#### Sesi Presensi

- [x] **Admin — Sesi** — index ✅ | store (buka) ✅ | show (live radar) ✅ | tutup ✅
- [x] **Dosen — Sesi** — index ✅ | store (buka + GPS toggle) ✅ | show ✅ | live ✅ | liveData (JSON + haversine) ✅ | tutup ✅

#### Riwayat & Override

- [x] **Admin — Riwayat** — index (daftar kelas) ✅ | show (matriks kehadiran) ✅ | overridePresensi (H/A/S/I) ✅
- [x] **Dosen — Riwayat** — index (kelas milik dosen) ✅ | show (matriks kehadiran) ✅

#### Laporan

- [ ] **Laporan Presensi** — route ada (`admin.laporan.presensi`) tapi hanya mengembalikan string teks `'Halaman Laporan Presensi (Sedang Dibangun)'`. Belum ada controller, view, maupun export PDF.

#### Helper & Service Class

- [x] **`app/Helpers/GpsHelper.php`** — Haversine formula diekstrak dari Dosen controller. Digunakan oleh `PresensiApiController` dan `Dosen\SesiPresensiController`.
- [ ] **`app/Services/FaceRecognitionService.php`** — _file tidak ada_. Validasi face di PresensiApiController sudah inline (cek `face_match` + `face_confidence >= 0.80`). Class terpisah diperlukan jika logika validasi berkembang.

---

### API Endpoint (Flutter ↔ Laravel)

| #   | Method | Endpoint                        | Status | Catatan                                              |
| --- | ------ | ------------------------------- | ------ | ---------------------------------------------------- |
| 1   | POST   | `/api/v1/login`                 | ✅     | Fully implemented, login by NIM                      |
| 2   | POST   | `/api/v1/logout`                | ✅     | Revoke Sanctum token                                 |
| 3   | GET    | `/api/v1/profile`               | ✅     | Return user profile data                             |
| 4   | GET    | `/api/v1/jadwal/hari-ini`       | ✅     | Filter berdasarkan kelas yang diikuti mahasiswa      |
| 5   | GET    | `/api/v1/sesi/aktif/{kelas_id}` | ✅     | Return sesi aktif + koordinat ruangan                |
| 6   | POST   | `/api/v1/presensi`              | ✅     | Implementasi lengkap 8 langkah validasi A–H          |
| 7   | GET    | `/api/v1/presensi/riwayat`      | ✅     | Return riwayat presensi mahasiswa yang login         |

#### Validasi Presensi — 8 Langkah A–H (PresensiApiController::store)

- [x] **A** — Cek sesi aktif → gagal: `sesi_tutup`
- [x] **B** — Cek sudah presensi → gagal: `sudah_presensi`
- [x] **C** — Cek `is_dev_mode` → gagal: `developer_mode_aktif`
- [x] **D** — Cek `is_mock_location` → gagal: `mock_location`
- [x] **E** — Haversine GPS vs radius ruangan → gagal: `luar_radius`
- [x] **F** — Cek foto referensi wajah ada → gagal: `foto_referensi_tidak_ada`
- [x] **G** — Validasi face ML Kit (confidence ≥ 0.80) → gagal: `wajah_tidak_cocok`
- [x] **H** — Semua lolos → simpan record Presensi dengan status `Hadir`

---

### Frontend Web (Blade Views)

#### Halaman Admin

| Halaman                    | File                                  | Status |
| -------------------------- | ------------------------------------- | ------ |
| Dashboard                  | `admin/dashboard.blade.php`           | ✅     |
| Data Dosen                 | `admin/dosen/index.blade.php`         | ✅     |
| Data Mahasiswa             | `admin/mahasiswa/index.blade.php`     | ✅     |
| Data Mata Kuliah           | `admin/mata-kuliah/index.blade.php`   | ✅     |
| Data Ruangan (+ peta)      | `admin/ruangan/index.blade.php`       | ✅     |
| Tahun Ajaran               | `admin/tahun_ajaran/index.blade.php`  | ✅     |
| Data Kelas Perkuliahan     | `admin/kelas/index.blade.php`         | ✅     |
| Kelola Peserta Kelas       | `admin/kelas/peserta/index.blade.php` | ✅     |
| Jadwal Perkuliahan         | `admin/jadwal/index.blade.php`        | ✅     |
| Monitor Sesi (daftar)      | `admin/sesi/index.blade.php`          | ✅     |
| Monitor Sesi (live radar)  | `admin/sesi/live.blade.php`           | ✅     |
| Riwayat Presensi (daftar)  | `admin/riwayat/index.blade.php`       | ✅     |
| Riwayat Presensi (matriks) | `admin/riwayat/show.blade.php`        | ✅     |
| Profil                     | `admin/profile/edit.blade.php`        | ✅     |
| **Laporan Presensi**       | _(belum dibuat)_                      | ❌     |

#### Halaman Dosen

| Halaman                     | File                            | Status |
| --------------------------- | ------------------------------- | ------ |
| Dashboard                   | `dosen/dashboard.blade.php`     | ✅     |
| Kelola Sesi (daftar + buka) | `dosen/sesi/index.blade.php`    | ✅     |
| Detail Sesi                 | `dosen/sesi/show.blade.php`     | ✅     |
| Live Monitor Sesi           | `dosen/sesi/live.blade.php`     | ✅     |
| Riwayat Presensi (daftar)   | `dosen/riwayat/index.blade.php` | ✅     |
| Riwayat Presensi (matriks)  | `dosen/riwayat/show.blade.php`  | ✅     |

---

### Flutter Mobile

> Folder project Flutter (`C:\Skripsi_Presensi_Rizaldi\mobile_presensi`) **belum dibuat**.
> Semua item di bawah adalah 0%.

- [ ] Setup project + pubspec.yaml (semua package)
- [ ] Konstanta API (`lib/core/constants/api_constants.dart`)
- [ ] ApiService class (`lib/core/services/api_service.dart`)
- [ ] FaceRecognitionService (`lib/core/services/face_recognition_service.dart`)
- [ ] Login screen + Sanctum token (SharedPreferences)
- [ ] Dashboard + jadwal kuliah hari ini
- [ ] Deteksi Developer Mode (`device_info_plus`)
- [ ] Deteksi Mock Location (`geolocator`)
- [ ] Validasi GPS — kirim koordinat ke Laravel
- [ ] Face Recognition ML Kit on-device (cosine similarity ≥ 0.80)
- [ ] Flow presensi lengkap (GPS → Face → Submit)
- [ ] Riwayat presensi mahasiswa
- [ ] Profil mahasiswa

---

## Bug & Inkonsistensi yang Ditemukan

### ~~Bug 1~~ ✅ DIPERBAIKI — Tabel `presensis`: Kolom GPS salah tipe

Migration `2026_05_11_131931_update_presensis_table` telah mengubah `latitude` dan `longitude` dari `VARCHAR` menjadi `DECIMAL(10,8)` dan `DECIMAL(11,8)`.

---

### ~~Bug 2~~ ✅ DIPERBAIKI — Tabel `presensis`: Kolom penting untuk API hilang

Migration yang sama telah menambahkan semua kolom yang hilang: `jarak_meter`, `face_confidence`, `face_verified`, `alasan_ditolak`, `override_by`.

---

### Bug 3 — Tabel `presensis`: Enum `status_kehadiran` tidak konsisten

**File:** `database/migrations/2026_04_22_190000_create_presensis_table.php` baris 29

```php
// YANG ADA DI MIGRATION:
->enum('status_kehadiran', ['Hadir', 'Terlambat', 'Izin', 'Sakit', 'Alfa'])

// YANG DIPAKAI DI CONTROLLER (RiwayatPresensiController):
$statusMap = ['H' => 'Hadir', 'S' => 'Sakit', 'I' => 'Izin'];
// Override hanya mengenal: H, A (hapus record), S, I
// 'Terlambat' dan 'Alfa' tidak pernah dipakai di mana pun
```

**Dampak:** `Alfa` dan `Terlambat` ada di DB tapi tidak bisa di-set lewat UI/API.
**Solusi:** Seragamkan enum menjadi `['Hadir', 'Sakit', 'Izin']` saja (Alfa = tidak ada record).

---

### Bug 4 — API `/jadwal/hari-ini` tidak filter kelas mahasiswa

**File:** `app/Http/Controllers/Api/JadwalApiController.php` baris 28–34

```php
// YANG ADA: ambil SEMUA jadwal hari ini
$jadwals = JadwalPerkuliahan::with([...])->where('hari', $hariIni)->get();

// SEHARUSNYA: filter hanya kelas yang diikuti mahasiswa yang login
$mahasiswaId = auth()->user()->/* relasi ke mahasiswas */->id;
$kelasIds = PesertaKelas::where('mahasiswa_id', $mahasiswaId)->pluck('kelas_perkuliahan_id');
$jadwals = JadwalPerkuliahan::whereIn('kelas_perkuliahan_id', $kelasIds)->where('hari', $hariIni)->get();
```

**Dampak:** Mahasiswa akan melihat jadwal semua kelas, bukan hanya kelas yang ia ikuti.

---

### Bug 5 — CLAUDE.md mencatat bug `admin/sesi/live.blade.php` yang sudah diperbaiki

**Lokasi:** CLAUDE.md bagian "Bug Diketahui"

CLAUDE.md menyebut `$sesi->kelasPerkuliahan->ruangan` masih salah di baris 37.
Namun setelah inspeksi aktual, **kode sudah benar** — baris 37 sudah menggunakan:

```php
$sesi->jadwalPerkuliahan->ruangan->radius_meter
```

CLAUDE.md perlu diperbarui untuk menghapus catatan bug ini.

---

### Inkonsistensi 1 — Format response API

**CLAUDE.md menentukan:** `{"success": true, "message": "...", "data": {...}}`
**Implementasi aktual menggunakan:** `{"status": true, "message": "...", "data": {...}}`

Seluruh implementasi API (`AuthApiController`, `JadwalApiController`, `PresensiApiController`) konsisten menggunakan key `status`. CLAUDE.md yang perlu disesuaikan.

---

### Inkonsistensi 2 — Nama tabel di CLAUDE.md vs migrasi aktual

CLAUDE.md mendokumentasikan skema lama dengan nama tabel prefiks `m_` dan `t_`:

| Di CLAUDE.md            | Nama aktual di migrasi |
| ----------------------- | ---------------------- |
| `m_user`                | `users`                |
| `m_roles`               | _(tidak ada)_          |
| `m_permissions`         | _(tidak ada)_          |
| `roles_has_permissions` | _(tidak ada)_          |
| `t_kelas_perkuliahan`   | `kelas_perkuliahans`   |
| `t_peserta_kelas`       | `peserta_kelas`        |
| `t_sesi_presensi`       | `sesi_presensis`       |
| `t_presensi`            | `presensis`            |

Sistem role tidak menggunakan tabel `m_roles` — role disimpan sebagai kolom string `role` di tabel `users`. Ini sudah benar di kode, tapi CLAUDE.md belum diupdate.

---

### ~~Inkonsistensi 3~~ ✅ DIPERBAIKI — GpsHelper

`app/Helpers/GpsHelper.php` telah dibuat. `FaceRecognitionService` belum dibuat tapi validasi face sudah inline di `PresensiApiController::store()` (cukup untuk saat ini).

---

## Yang Harus Dikerjakan Selanjutnya

### Prioritas 1 — ✅ SELESAI

1. [x] Migration perbaikan tabel `presensis` (tipe kolom decimal, nullable foto_wajah, enum 3 nilai, kolom baru)
2. [x] Migration tambah kolom `foto_referensi` di tabel `mahasiswas`
3. [x] `app/Helpers/GpsHelper.php` — Haversine diekstrak, dipakai oleh API dan Dosen controller
4. [x] `app/Http/Requests/StorePresensiRequest.php` — validasi request presensi
5. [x] `PresensiApiController::store()` — 8 langkah validasi A–H fully implemented
6. [x] `PresensiApiController::riwayat()` — riwayat presensi mahasiswa
7. [x] `JadwalApiController::hariIni()` — filter berdasarkan kelas yang diikuti mahasiswa
8. [x] `MahasiswaController::update()` — upload foto referensi wajah ke private storage
9. [x] View edit mahasiswa — tambah field foto referensi dengan status indikator

### Prioritas 2 — Fitur Utama yang Belum Selesai

6. **Halaman Laporan Presensi (Admin)**
    - Buat `Admin\LaporanPresensiController` dengan method `index()` dan `exportPdf()`
    - Buat view `admin/laporan/index.blade.php` dengan filter tahun ajaran / kelas / dosen
    - Export PDF menggunakan `barryvdh/laravel-dompdf` (sudah terinstall)
    - Hapus placeholder string di `routes/web.php` baris 94

7. **Buat `app/Services/FaceRecognitionService.php`**
    - Validasi input dari Flutter: `face_match` (bool) + `face_confidence` (float)
    - Terapkan threshold: confidence ≥ 0.80 dianggap cocok
    - Dapat dipanggil dari PresensiApiController::store() (Langkah G)

### Prioritas 3 — Penyempurnaan

8. **Dosen — Override Presensi**
    - Dosen\RiwayatPresensiController belum punya method `overridePresensi()`
    - Saat ini hanya Admin yang bisa override
    - Tambahkan kemampuan override di halaman `dosen/riwayat/show.blade.php`

9. **API `/api/v1/jadwal/hari-ini` — Sertakan data `kelas_id`**
    - Response saat ini tidak menyertakan `kelas_perkuliahan_id`
    - Flutter perlu nilai ini untuk memanggil `/sesi/aktif/{kelas_id}`

10. **Sinkronkan CLAUDE.md dengan kondisi aktual**
    - Perbarui nama tabel (hapus prefiks `m_`/`t_`)
    - Perbaiki format response API: `success` → `status`
    - Hapus catatan bug `admin/sesi/live.blade.php` yang sudah diperbaiki
    - Update checklist: hapus centang GpsHelper dan FaceRecognitionService
    - Update format response API menjadi `{"status": bool, ...}`

### Prioritas 4 — Flutter Mobile (mulai setelah Prioritas 1 selesai)

11. Setup project Flutter + `pubspec.yaml` semua package
12. `lib/core/constants/api_constants.dart` — base URL WiFi lokal
13. `lib/core/services/api_service.dart` — wrapper HTTP semua endpoint
14. Login screen + simpan token ke `SharedPreferences`
15. Dashboard — tampilkan jadwal hari ini (filter kelas mahasiswa)
16. Deteksi Developer Mode (`device_info_plus`)
17. Deteksi Mock Location (`geolocator`)
18. GPS validation — kirim koordinat ke Laravel
19. Face Recognition ML Kit on-device (download foto referensi → embedding → cosine similarity)
20. Flow presensi lengkap (GPS check → Face check → POST /presensi)
21. Riwayat presensi mahasiswa (GET /presensi/riwayat)
22. Profil mahasiswa (GET /profile)

### Prioritas 5 — Testing & Skripsi

23. Black Box Testing semua role (Admin, Dosen, Mahasiswa)
24. Pengujian GPS lapangan — min. 10 titik di FT UNMUL + hitung MAPE
25. Pengujian deteksi Developer Mode & Mock Location
26. End-to-end test full flow presensi (dari buka sesi hingga rekap hadir)
27. Screenshot semua fitur untuk Bab IV skripsi
28. Tulis Bab IV (implementasi + hasil pengujian)
29. Tulis Bab V (kesimpulan + saran)

---

## Catatan Teknis

### 1. Haversine ada di dua tempat yang berbeda

Rumus haversine saat ini ada di `Dosen\SesiPresensiController::haversine()` (untuk live monitor web)
dan perlu diimplementasi ulang di `PresensiApiController::store()` (untuk API).
Ekstrak ke `GpsHelper` sebelum menulis `store()` agar tidak duplikasi.

### 2. Foto referensi wajah belum punya kolom di tabel `users`

Berdasarkan inspeksi `users` migration, tidak ditemukan kolom `foto_referensi` atau `face_id`.
CLAUDE.md menyebutkan kolom ini tapi migration tidak menambahkannya.
Perlu dicek dan ditambahkan jika belum ada, karena Langkah F dan G validasi presensi bergantung pada ini.

### 3. `JadwalApiController::hariIni()` tidak filter by mahasiswa

Endpoint ini mengembalikan semua jadwal hari ini dari semua kelas.
Mahasiswa yang terdaftar di 2 kelas dan ada 10 kelas lain di hari yang sama akan menerima 12 jadwal.
Perlu ditambahkan filter `WHERE kelas_perkuliahan_id IN (SELECT kelas_perkuliahan_id FROM peserta_kelas WHERE mahasiswa_id = ?)`.

### 4. File `foto_wajah` di `presensis` bukan nullable

Migration mendefinisikan `$table->string('foto_wajah')` tanpa `->nullable()`.
Artinya setiap record presensi **wajib** menyertakan foto. Pastikan PresensiApiController
menyimpan foto sebelum insert, atau tambahkan `->nullable()` jika foto opsional.

### 5. `peserta_kelas` tidak punya unique constraint yang di-enforce di semua jalur

Migration `create_peserta_kelas_table` sudah punya `$table->unique(['kelas_perkuliahan_id', 'mahasiswa_id'])`.
Controller `PesertaKelasController::store()` juga mengecek duplikat secara manual.
Tapi `PesertaKelasImport` hanya menggunakan skip jika NIM tidak ditemukan — perlu tambahkan skip jika sudah terdaftar.

### 6. Route `admin.kelas` menggunakan resource route tapi hanya butuh sebagian method

`Route::resource('kelas', KelasPerkuliahanController::class)` mendaftarkan 7 route (index, create, store, show, edit, update, destroy).
`create`, `show`, dan `edit` kemungkinan tidak punya view. Pertimbangkan `->only(['index', 'store', 'update', 'destroy'])`.

---

_Update file ini setiap kali milestone selesai._
_Centang item di atas dan perbarui persentase progres._
