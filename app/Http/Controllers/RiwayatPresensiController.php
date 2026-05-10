<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\SesiPresensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatPresensiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Presensi::with([
            'mahasiswa',
            'sesiPresensi.jadwalPerkuliahan.kelasPerkuliahan.mataKuliah',
            'sesiPresensi.jadwalPerkuliahan.kelasPerkuliahan.dosen',
            'sesiPresensi.jadwalPerkuliahan.ruangan',
        ]);

        if ($user->isDosen()) {
            $dosenId = $user->dosen?->id;

            if (! $dosenId) {
                return view('riwayat.index', [
                    'presensiList' => collect()->paginate(15),
                    'stats'        => $this->emptyStats(),
                    'filters'      => $request->only(['search', 'status', 'tanggal']),
                ]);
            }

            $query->whereHas(
                'sesiPresensi.jadwalPerkuliahan.kelasPerkuliahan',
                fn ($q) => $q->where('dosen_id', $dosenId)
            );
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('mahasiswa', fn ($m) => $m->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%"))
                  ->orWhereHas(
                      'sesiPresensi.jadwalPerkuliahan.kelasPerkuliahan.mataKuliah',
                      fn ($m) => $m->where('nama_mk', 'like', "%{$search}%")
                  );
            });
        }

        if ($request->filled('status')) {
            $query->where('status_kehadiran', $request->status);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('waktu_absen', $request->tanggal);
        }

        $presensiList = $query->latest('waktu_absen')->paginate(15)->withQueryString();

        $statsQuery = Presensi::query();
        if ($user->isDosen()) {
            $statsQuery->whereHas(
                'sesiPresensi.jadwalPerkuliahan.kelasPerkuliahan',
                fn ($q) => $q->where('dosen_id', $user->dosen?->id)
            );
        }
        $stats = [
            'total'    => $statsQuery->count(),
            'hadir'    => (clone $statsQuery)->where('status_kehadiran', 'Hadir')->count(),
            'terlambat'=> (clone $statsQuery)->where('status_kehadiran', 'Terlambat')->count(),
            'izin'     => (clone $statsQuery)->whereIn('status_kehadiran', ['Izin', 'Sakit'])->count(),
            'alfa'     => (clone $statsQuery)->where('status_kehadiran', 'Alfa')->count(),
        ];

        return view('riwayat.index', compact('presensiList', 'stats') + ['filters' => $request->only(['search', 'status', 'tanggal'])]);
    }

    private function emptyStats(): array
    {
        return ['total' => 0, 'hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'alfa' => 0];
    }
}
