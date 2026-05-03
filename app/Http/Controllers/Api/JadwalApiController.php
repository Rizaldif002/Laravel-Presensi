<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalPerkuliahan;
use App\Models\Mahasiswa;
use App\Models\SesiPresensi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JadwalApiController extends Controller
{
    private static array $hariMap = [
        'Sunday'    => 'Minggu',
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu',
    ];

    public function hariIni(Request $request): JsonResponse
    {
        $hariIni = self::$hariMap[now()->format('l')];

        $jadwals = JadwalPerkuliahan::with([
            'kelasPerkuliahan.mataKuliah',
            'kelasPerkuliahan.dosen',
            'kelasPerkuliahan.tahunAjaran',
            'ruangan',
            'sesiPresensis' => fn ($q) => $q->where('status', 'aktif')->latest()->limit(1),
        ])->where('hari', $hariIni)->get();

        $data = $jadwals->map(function ($jadwal) {
            $sesiAktif = $jadwal->sesiPresensis->first();

            return [
                'jadwal_id'    => $jadwal->id,
                'hari'         => $jadwal->hari,
                'jam_mulai'    => substr($jadwal->jam_mulai, 0, 5),
                'jam_selesai'  => substr($jadwal->jam_selesai, 0, 5),
                'mata_kuliah'  => $jadwal->kelasPerkuliahan->mataKuliah->nama_mk ?? null,
                'kode_mk'      => $jadwal->kelasPerkuliahan->mataKuliah->kode_mk ?? null,
                'nama_kelas'   => $jadwal->kelasPerkuliahan->nama_kelas ?? null,
                'dosen'        => $jadwal->kelasPerkuliahan->dosen->nama_dosen ?? null,
                'tahun_ajaran' => $jadwal->kelasPerkuliahan->tahunAjaran->tahun_ajaran ?? null,
                'ruangan'      => $jadwal->ruangan?->nama_ruangan,
                'gedung'       => $jadwal->ruangan?->gedung,
                'sesi_aktif'   => $sesiAktif ? [
                    'sesi_id'    => $sesiAktif->id,
                    'waktu_buka' => $sesiAktif->waktu_buka,
                    'status'     => $sesiAktif->status,
                ] : null,
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => "Jadwal hari {$hariIni}, " . now()->format('d/m/Y') . '.',
            'data'    => $data,
        ]);
    }

    public function sesiAktif(Request $request, int $kelasId): JsonResponse
    {
        $sesi = SesiPresensi::with([
            'jadwalPerkuliahan.kelasPerkuliahan.mataKuliah',
            'jadwalPerkuliahan.kelasPerkuliahan.tahunAjaran',
            'jadwalPerkuliahan.ruangan',
        ])->whereHas(
            'jadwalPerkuliahan',
            fn ($q) => $q->where('kelas_perkuliahan_id', $kelasId)
        )->where('status', 'aktif')->latest()->first();

        if (! $sesi) {
            return response()->json([
                'status'  => false,
                'message' => 'Tidak ada sesi presensi aktif untuk kelas ini.',
                'data'    => null,
            ], 404);
        }

        $ruangan = $sesi->jadwalPerkuliahan->ruangan;

        return response()->json([
            'status'  => true,
            'message' => 'Sesi presensi aktif ditemukan.',
            'data'    => [
                'sesi_id'      => $sesi->id,
                'status'       => $sesi->status,
                'waktu_buka'   => $sesi->waktu_buka,
                'mata_kuliah'  => $sesi->jadwalPerkuliahan->kelasPerkuliahan->mataKuliah->nama_mk ?? null,
                'nama_kelas'   => $sesi->jadwalPerkuliahan->kelasPerkuliahan->nama_kelas ?? null,
                'hari'         => $sesi->jadwalPerkuliahan->hari,
                'jam_mulai'    => substr($sesi->jadwalPerkuliahan->jam_mulai, 0, 5),
                'jam_selesai'  => substr($sesi->jadwalPerkuliahan->jam_selesai, 0, 5),
                'ruangan'      => [
                    'nama'         => $ruangan?->nama_ruangan,
                    'gedung'       => $ruangan?->gedung,
                    'latitude'     => $ruangan?->latitude ? (float) $ruangan->latitude : null,
                    'longitude'    => $ruangan?->longitude ? (float) $ruangan->longitude : null,
                    'radius_meter' => $ruangan?->radius_meter ?? 50,
                ],
            ],
        ]);
    }
}
