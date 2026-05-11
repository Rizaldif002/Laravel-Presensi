# CLAUDE.md — Sistem Presensi Hybrid

# Terakhir diperbarui: Mei 2026

> Baca file ini sebelum mengerjakan APAPUN di project ini.
> Berisi konteks lengkap, status terkini, konvensi koding, dan hal yang TIDAK BOLEH diubah.

---

## Identitas Project

**Judul Skripsi:**
Prototipe Sistem Presensi Hybrid Berbasis Web dan Mobile Native
dengan Validasi GPS dan Foto Selfie (Face Recognition)
Menggunakan Framework Laravel dan Flutter

**Mahasiswa:** Muhammad Rizaldi Nur
**Jurusan:** Teknik Elektro, Universitas Mulawarman (UNMUL)
**Folder project Laravel:** `C:\xampp\htdocs\Laravel-Presensi`
**Folder project Flutter:** `C:\Skripsi_Presensi_Rizaldi\mobile_presensi` (belum dibuat)

---

## Tech Stack

### Backend — Laravel

- **Framework:** Laravel 12 (bukan 11 seperti sebelumnya)
- **PHP:** 8.2.x
- **Database:** MySQL via XAMPP
- **Auth Web:** Laravel Session (Admin & Dosen via browser)
- **Auth API:** Laravel Sanctum (Mahasiswa via Flutter)
- **UI:** Breeze + Tailwind CSS + Alpine.js
- **PDF Export:** barryvdh/laravel-dompdf
- **Excel Import:** Maatwebsite\Excel
- **Port development:** 8000

### Frontend Web

- Blade templating + Tailwind CSS
- Alpine.js untuk interaktivitas sidebar dropdown
- Leaflet.js + OpenStreetMap untuk peta geofencing (CDN)
- SweetAlert2 untuk modal/alert (CDN)
- Vite untuk compile assets

### Mobile — Flutter

- **Framework:** Flutter 3.41.9 + Dart (stable, sudah terinstall)
- **Target:** Android (minimal Android 10)
- **Face Recognition:** Google ML Kit on-device (BUKAN Python)
- **HTTP Client:** http package
- **GPS:** geolocator
- **Kamera:** camera
- **Face Detection/Recognition:** google_mlkit_face_detection
- **Anti-Cheat:** device_info_plus
- **Token Storage:** shared_preferences
- **Temp File:** path_provider
- **Permission:** permission_handler

### ⚠️ TIDAK ADA Python Microservice

Arsitektur sebelumnya menggunakan Python untuk Face Recognition.
Keputusan final: Face Recognition menggunakan Google ML Kit on-device di Flutter.
Tidak ada server Python, tidak ada HTTP call ke API eksternal untuk face.
Semua proses wajah terjadi langsung di HP mahasiswa.

---

## Arsitektur Sistem

```
┌──────────────────────────────────────────────────┐
│  CLIENT LAYER                                     │
│  [Browser] Admin & Dosen    [Flutter Android]     │
│  Blade + Tailwind           Mahasiswa             │
└────────────┬───────────────────────┬──────────────┘
             │ Session Auth          │ Sanctum Token
             ▼                       ▼
┌──────────────────────────────────────────────────┐
│  SERVER — Laravel 12                             │
│  Web Controllers (Admin & Dosen panel)           │
│  API Controllers (7 endpoints untuk Flutter)     │
│  GpsHelper (Haversine Formula)                   │
│  FaceRecognitionService (validasi hasil ML Kit)  │
└─────────────────────┬────────────────────────────┘
                      ▼
┌──────────────────────────────────────────────────┐
│  DATA LAYER                                       │
│  MySQL (11 tabel)                                 │
│  File Storage (foto selfie — bukti visual)        │
└──────────────────────────────────────────────────┘

Face Recognition Flow (ML Kit on-device):
Flutter download foto referensi dari Laravel
  → ML Kit ekstrak face embedding foto referensi
  → ML Kit ekstrak face embedding foto selfie
  → Hitung cosine similarity (threshold: 0.80)
  → Kirim hasil (is_match + confidence) ke Laravel
  → Laravel validasi dan simpan presensi
```

---

## Database — 11 Tabel

```
m_roles               → role pengguna
m_permissions         → permission granular
roles_has_permissions → pivot role-permission
m_user                → semua pengguna (admin, dosen, mahasiswa)
                        kolom: role (string), nim, nip,
                        foto_referensi (path), face_id
m_tahun_ajaran        → periode akademik
m_mata_kuliah         → data mata kuliah
m_ruangan             → ruangan + GPS
                        latitude DECIMAL(10,8)
                        longitude DECIMAL(11,8)
                        radius_meter INT
t_kelas_perkuliahan   → kelas perkuliahan
t_peserta_kelas       → pivot mahasiswa-kelas
t_sesi_presensi       → sesi presensi (dibuka_oleh FK ke m_user)
t_presensi            → record kehadiran
                        kolom: latitude_presensi DECIMAL(10,8),
                        longitude_presensi DECIMAL(11,8),
                        jarak_meter DECIMAL(8,2),
                        face_confidence DECIMAL(5,2),
                        face_verified BOOLEAN,
                        alasan_ditolak VARCHAR(100),
                        override_by INT FK
```

### Relasi Penting — JANGAN SALAH

```php
// Path BENAR dari SesiPresensi ke Ruangan:
$sesi->jadwalPerkuliahan->ruangan

// BUKAN ini (bug lama yang sudah diketahui):
$sesi->kelasPerkuliahan->ruangan  // ← SALAH

// Path benar ke Mata Kuliah dari sesi:
$sesi->jadwalPerkuliahan->kelasPerkuliahan->mataKuliah

// Kolom radius:
$ruangan->radius_meter  // ← BUKAN $ruangan->radius

// Kolom GPS ruangan — DECIMAL bukan VARCHAR:
$ruangan->latitude   // DECIMAL(10,8)
$ruangan->longitude  // DECIMAL(11,8)
```

### Bug Diketahui

`resources/views/admin/sesi/live.blade.php` baris 37 masih menggunakan
`$sesi->kelasPerkuliahan->ruangan` yang salah.
Harus diganti ke `$sesi->jadwalPerkuliahan->ruangan`.

---

## Status Pengerjaan Terkini

### ✅ Selesai — Laravel

- [x] Setup project Laravel 12 + 11 tabel migration
- [x] Auth multi-role Admin & Dosen (Session)
- [x] RoleMiddleware — redirect per role (bukan abort 403)
- [x] CRUD Data Master: Ruangan+GPS, Mata Kuliah, Tahun Ajaran
- [x] Manajemen User: Dosen + Mahasiswa (CRUD + upload foto referensi)
- [x] Kelola Kelas Perkuliahan + Jadwal
- [x] Sesi Presensi (buka/tutup + live monitor mahasiswa hadir)
- [x] Excel Import: Dosen, Mahasiswa, MataKuliah, Kelas
- [x] Laravel Sanctum + 7 API Endpoint
- [x] GpsHelper (Haversine Formula)
- [x] FaceRecognitionService (versi ML Kit)
- [x] StorePresensiRequest (versi ML Kit — face_match + face_confidence)
- [x] Sidebar redesign: collapsible dropdown Alpine.js
- [x] Sidebar restrukturisasi: group "Presensi" (Kelola Sesi + Riwayat)
- [x] Riwayat Presensi (controller + view + filter + pagination)
- [x] DomPDF reports (laporan presensi dasar)

### 🔲 Belum Selesai — Laravel (KERJAKAN URUT)

- [ ] PresensiApiController.store() — 8 langkah validasi berurutan
- [ ] Laporan Presensi halaman lengkap + Export PDF template
- [ ] Validasi Manual Presensi oleh Dosen (override_by)

### 🔲 Belum Dimulai — Flutter

- [ ] Setup project + semua package (pubspec.yaml)
- [ ] Login screen + Sanctum token management
- [ ] Dashboard + jadwal kuliah hari ini
- [ ] Deteksi Developer Mode (device_info_plus)
- [ ] Deteksi Mock Location (geolocator)
- [ ] Validasi GPS (kirim koordinat ke Laravel)
- [ ] Face Recognition ML Kit on-device
- [ ] Riwayat presensi mahasiswa
- [ ] Profil mahasiswa

### 🔲 Belum Dimulai — Testing & Skripsi

- [ ] Black Box Testing semua role
- [ ] Pengujian GPS lapangan + MAPE (min 10 titik FT UNMUL)
- [ ] Pengujian deteksi Dev Mode & Mock Location
- [ ] End-to-end test full flow
- [ ] Isi Bab IV skripsi (screenshot + hasil pengujian)
- [ ] Tulis Bab V skripsi (kesimpulan + saran)

---

## Role & Hak Akses

| Role      | Platform        | Auth          | Akses                                      |
| --------- | --------------- | ------------- | ------------------------------------------ |
| admin     | Laravel Web     | Session       | CRUD semua data, laporan semua kelas       |
| dosen     | Laravel Web     | Session       | RU kelas, CRUD sesi, laporan kelas sendiri |
| mahasiswa | Flutter Android | Sanctum Token | Input presensi, riwayat sendiri            |

**Cara cek role (TIDAK ADA model Role terpisah — role adalah kolom string di users):**

```php
$user->role === 'admin'     // cek langsung
$user->isAdmin()            // method helper di User model
$user->isDosen()            // method helper di User model
$user->isMahasiswa()        // method helper di User model
```

---

## API Endpoint — Flutter ↔ Laravel

Semua endpoint prefix `/api/v1/`

| Method | Endpoint                      | Auth   | Fungsi                           |
| ------ | ----------------------------- | ------ | -------------------------------- |
| POST   | /api/v1/login                 | Public | Login mahasiswa (NIM + password) |
| POST   | /api/v1/logout                | Token  | Hapus token                      |
| GET    | /api/v1/profile               | Token  | Data profil mahasiswa            |
| GET    | /api/v1/jadwal/hari-ini       | Token  | Jadwal + status sesi aktif       |
| GET    | /api/v1/sesi/aktif/{kelas_id} | Token  | Sesi aktif + koordinat ruangan   |
| POST   | /api/v1/presensi              | Token  | Submit presensi                  |
| GET    | /api/v1/presensi/riwayat      | Token  | Riwayat kehadiran                |

### Input POST /api/v1/presensi (versi ML Kit terbaru)

```json
{
    "kelas_id": 1,
    "latitude": -0.5025,
    "longitude": 117.1535,
    "is_dev_mode": false,
    "is_mock_location": false,
    "face_match": true,
    "face_confidence": 0.87,
    "foto_selfie": "base64_string_foto_sebagai_bukti"
}
```

### Format Response Standar

```json
// Berhasil:
{"success": true, "message": "...", "data": {...}}

// Gagal:
{"success": false, "reason": "kode_reason", "message": "pesan untuk Flutter"}
```

### Kode Reason Presensi Gagal

`sesi_tutup` | `sudah_presensi` | `developer_mode_aktif` |
`mock_location` | `luar_radius` | `wajah_tidak_cocok` |
`foto_referensi_tidak_ada`

---

## Urutan Validasi Presensi — WAJIB BERURUTAN

```
A → Cek sesi aktif          : tidak ada → sesi_tutup
B → Cek sudah presensi      : sudah → sudah_presensi
C → Cek is_dev_mode         : true → developer_mode_aktif
D → Cek is_mock_location    : true → mock_location
E → Haversine GPS           : > radius_meter → luar_radius
F → Cek foto_referensi ada  : kosong → foto_referensi_tidak_ada
G → Validasi face ML Kit    : confidence < 0.80 → wajah_tidak_cocok
H → Semua lolos             : simpan presensi status 'hadir'
```

---

## Haversine Formula

```php
// app/Helpers/GpsHelper.php
public static function hitungJarak(
    float $latMahasiswa, float $lonMahasiswa,
    float $latRuangan,   float $lonRuangan
): float {
    $r = 6371000;
    $dLat = deg2rad($latRuangan - $latMahasiswa);
    $dLon = deg2rad($lonRuangan - $lonMahasiswa);
    $a = sin($dLat/2)**2 +
         cos(deg2rad($latMahasiswa)) *
         cos(deg2rad($latRuangan)) *
         sin($dLon/2)**2;
    return $r * 2 * atan2(sqrt($a), sqrt(1-$a));
}
```

---

## pubspec.yaml Flutter

```yaml
dependencies:
    flutter:
        sdk: flutter
    http: ^1.2.0
    geolocator: ^11.0.0
    camera: ^0.10.5
    google_mlkit_face_detection: ^0.9.0
    device_info_plus: ^9.1.0
    shared_preferences: ^2.2.2
    path_provider: ^2.1.2
    permission_handler: ^11.3.0
    intl: ^0.19.0
```

---

## Struktur Folder Flutter

```
lib/
├── core/
│   ├── constants/
│   │   └── api_constants.dart    ← BASE URL di sini
│   └── services/
│       ├── api_service.dart
│       └── face_recognition_service.dart  ← file sudah disiapkan
├── features/
│   ├── auth/         → login_screen.dart
│   ├── dashboard/    → dashboard_screen.dart
│   ├── presensi/     → presensi_screen.dart
│   └── riwayat/      → riwayat_screen.dart
└── main.dart
```

---

## File Storage

```
storage/app/public/presensi/selfie/    → foto selfie (bukti visual)
storage/app/public/profil/             → foto profil pengguna
storage/app/private/wajah-referensi/   → foto referensi wajah (private)
```

**Aturan:**

- Foto referensi wajah: **private** — tidak bisa diakses via URL langsung
- Foto selfie presensi: **public** — bisa diakses sebagai bukti
- JANGAN simpan base64 di database
- Gunakan `Storage::disk('public')` untuk foto yang perlu diakses via URL

---

## Perintah yang Sering Digunakan

```bash
# Laravel
php artisan serve                    # start server port 8000
php artisan migrate                  # migration baru
php artisan migrate:fresh --seed     # reset + seed
php artisan optimize:clear           # clear semua cache
php artisan storage:link             # link storage ke public
php artisan route:list --path=api    # cek route API
php artisan tinker                   # REPL

# Flutter
flutter pub get                      # install package
flutter run                          # jalankan di device
flutter clean && flutter pub get     # reset dependency
flutter build apk --release          # build APK
```

---

## Konvensi Koding

### Laravel

```
- camelCase: PHP methods dan variables
- snake_case: nama kolom database
- PascalCase: class dan model
- Variabel domain: Bahasa Indonesia ($sesi, $ruangan, $mahasiswa)
- Validasi: Form Request class (bukan inline validate())
- N+1: selalu eager load with(['relation', 'nested'])
- Service class: jika method > 30 baris
- JANGAN query di dalam Blade @foreach
```

### Flutter

```
- Base URL: lib/core/constants/api_constants.dart
- Semua API call via ApiService class
- Token: SharedPreferences (bukan variable biasa)
- State: StatefulWidget untuk screen kompleks
```

---

## Koneksi Flutter ke Laravel Lokal

```
HP dan laptop harus WiFi yang SAMA.
Flutter tidak bisa akses localhost.

Cara cari IP: CMD → ipconfig → IPv4 di WiFi adapter
Contoh: http://192.168.1.5:8000

Simpan di lib/core/constants/api_constants.dart:
const String baseUrl = 'http://192.168.1.5:8000';
```

---

## Hal yang TIDAK BOLEH Dilakukan

```
❌ Jangan ubah session auth web (login Admin/Dosen sudah jalan)
❌ Jangan hapus/rename migration yang sudah di-run
❌ Jangan hardcode IP/URL di Flutter
❌ Jangan simpan token di variable biasa — wajib SharedPreferences
❌ Jangan ubah urutan validasi presensi A-H
❌ Jangan skip validasi GPS di server
❌ Jangan tambahkan Python dependency — tidak ada Python di project ini
❌ Jangan panggil HTTP ke Python untuk face recognition
❌ Jangan query di dalam Blade @foreach loop
❌ Jangan simpan base64 foto di database
```

---

_Update file ini setiap kali ada perubahan arsitektur atau milestone selesai._
_Centang checklist di Status Pengerjaan setelah setiap task selesai._
