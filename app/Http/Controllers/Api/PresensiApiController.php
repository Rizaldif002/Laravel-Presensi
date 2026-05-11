<?php

namespace App\Http\Controllers\Api;

use App\Helpers\GpsHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePresensiRequest;
use App\Models\Mahasiswa;
use App\Models\Presensi;
use App\Models\SesiPresensi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PresensiApiController extends Controller
{
    public function store(StorePresensiRequest $request): JsonResponse
    {
        $user = $request->user();

        $mahasiswa = Mahasiswa::where('nim', $user->nim)->first();
        if (! $mahasiswa) {
            return response()->json(['status' => false, 'reason' => 'data_tidak_valid', 'message' => 'Data mahasiswa tidak ditemukan.'], 404);
        }

        // A — Cek sesi aktif untuk kelas ini
        $sesi = SesiPresensi::with(['jadwalPerkuliahan.ruangan'])
            ->whereHas('jadwalPerkuliahan', fn ($q) => $q->where('kelas_perkuliahan_id', $request->kelas_id))
            ->where('status', 'aktif')
            ->latest()
            ->first();

        if (! $sesi) {
            return response()->json(['status' => false, 'reason' => 'sesi_tutup', 'message' => 'Tidak ada sesi presensi aktif untuk kelas ini.'], 422);
        }

        // B — Cek sudah presensi di sesi ini
        $sudah = Presensi::where('sesi_presensi_id', $sesi->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->exists();

        if ($sudah) {
            return response()->json(['status' => false, 'reason' => 'sudah_presensi', 'message' => 'Anda sudah melakukan presensi untuk sesi ini.'], 422);
        }

        // C — Cek developer mode
        if ($request->boolean('is_dev_mode')) {
            return response()->json(['status' => false, 'reason' => 'developer_mode_aktif', 'message' => 'Presensi tidak dapat dilakukan dalam mode developer.'], 422);
        }

        // D — Cek mock location
        if ($request->boolean('is_mock_location')) {
            return response()->json(['status' => false, 'reason' => 'mock_location', 'message' => 'Presensi tidak dapat dilakukan menggunakan lokasi palsu.'], 422);
        }

        // E — Haversine GPS vs radius ruangan
        $jarak   = null;
        $ruangan = $sesi->jadwalPerkuliahan->ruangan;

        if ($sesi->is_gps_enabled && $ruangan) {
            $jarak = GpsHelper::hitungJarak(
                (float) $request->latitude,
                (float) $request->longitude,
                (float) $ruangan->latitude,
                (float) $ruangan->longitude
            );

            if ($jarak > $ruangan->radius_meter) {
                return response()->json([
                    'status'  => false,
                    'reason'  => 'luar_radius',
                    'message' => 'Anda berada di luar radius presensi. Jarak Anda: ' . round($jarak) . ' m, Batas: ' . $ruangan->radius_meter . ' m.',
                ], 422);
            }
        }

        // F — Cek foto referensi wajah ada
        if (! $mahasiswa->foto_referensi || ! Storage::disk('local')->exists($mahasiswa->foto_referensi)) {
            return response()->json(['status' => false, 'reason' => 'foto_referensi_tidak_ada', 'message' => 'Foto referensi wajah belum diatur. Hubungi administrator.'], 422);
        }

        // G — Validasi hasil face recognition dari Flutter ML Kit
        if (! $request->boolean('face_match') || (float) $request->face_confidence < 0.80) {
            return response()->json([
                'status'  => false,
                'reason'  => 'wajah_tidak_cocok',
                'message' => 'Wajah tidak dikenali. Pastikan pencahayaan cukup dan wajah menghadap kamera langsung.',
            ], 422);
        }

        // H — Semua validasi lolos: simpan presensi
        $fotoPath = null;
        try {
            $base64    = preg_replace('#^data:image/\w+;base64,#i', '', $request->foto_selfie);
            $imageData = base64_decode($base64);
            $fileName  = $mahasiswa->id . '_' . $sesi->id . '_' . time() . '.jpg';
            $fotoPath  = 'presensi/selfie/' . $fileName;
            Storage::disk('public')->put($fotoPath, $imageData);
        } catch (\Exception) {
            $fotoPath = null;
        }

        $presensi = Presensi::create([
            'sesi_presensi_id' => $sesi->id,
            'mahasiswa_id'     => $mahasiswa->id,
            'waktu_absen'      => now(),
            'latitude'         => $request->latitude,
            'longitude'        => $request->longitude,
            'jarak_meter'      => $jarak !== null ? round($jarak, 2) : null,
            'foto_wajah'       => $fotoPath,
            'face_confidence'  => $request->face_confidence,
            'face_verified'    => true,
            'status_kehadiran' => 'Hadir',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Presensi berhasil dicatat.',
            'data'    => [
                'presensi_id'      => $presensi->id,
                'waktu_absen'      => $presensi->waktu_absen,
                'status_kehadiran' => $presensi->status_kehadiran,
                'jarak_meter'      => $presensi->jarak_meter,
                'face_confidence'  => $presensi->face_confidence,
            ],
        ]);
    }

    public function riwayat(Request $request): JsonResponse
    {
        $user = $request->user();

        $mahasiswa = Mahasiswa::where('nim', $user->nim)->first();
        if (! $mahasiswa) {
            return response()->json(['status' => false, 'message' => 'Data mahasiswa tidak ditemukan.', 'data' => []], 404);
        }

        $data = Presensi::with([
            'sesiPresensi.jadwalPerkuliahan.kelasPerkuliahan.mataKuliah',
            'sesiPresensi.jadwalPerkuliahan',
        ])
        ->where('mahasiswa_id', $mahasiswa->id)
        ->latest('waktu_absen')
        ->get()
        ->map(function ($p) {
            $jadwal = $p->sesiPresensi->jadwalPerkuliahan;
            $kelas  = $jadwal->kelasPerkuliahan;

            return [
                'presensi_id'      => $p->id,
                'sesi_id'          => $p->sesiPresensi->id,
                'mata_kuliah'      => $kelas->mataKuliah->nama_mk ?? null,
                'kode_mk'          => $kelas->mataKuliah->kode_mk ?? null,
                'nama_kelas'       => $kelas->nama_kelas ?? null,
                'hari'             => $jadwal->hari,
                'jam_mulai'        => substr($jadwal->jam_mulai, 0, 5),
                'jam_selesai'      => substr($jadwal->jam_selesai, 0, 5),
                'waktu_absen'      => $p->waktu_absen,
                'status_kehadiran' => $p->status_kehadiran,
                'jarak_meter'      => $p->jarak_meter,
                'face_verified'    => $p->face_verified,
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Riwayat presensi.',
            'data'    => $data,
        ]);
    }
}
