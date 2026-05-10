<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\JadwalPerkuliahan;
use App\Models\KelasPerkuliahan;
use App\Models\Ruangan;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class JadwalPerkuliahanController extends Controller
{
    public function index(Request $request)
    {
        $query = JadwalPerkuliahan::with([
            'kelasPerkuliahan.mataKuliah',
            'kelasPerkuliahan.dosen',
            'kelasPerkuliahan.tahunAjaran',
            'ruangan',
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('kelasPerkuliahan.mataKuliah', fn($q) => $q->where('nama_mk', 'like', '%' . $search . '%'))
                  ->orWhereHas('kelasPerkuliahan.dosen', fn($q) => $q->where('nama_dosen', 'like', '%' . $search . '%'))
                  ->orWhereHas('kelasPerkuliahan', fn($q) => $q->where('nama_kelas', 'like', '%' . $search . '%'));
            });
        }

        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        if ($request->filled('ruangan_id')) {
            $query->where('ruangan_id', $request->ruangan_id);
        }

        if ($request->filled('dosen_id')) {
            $query->whereHas('kelasPerkuliahan', fn($q) => $q->where('dosen_id', $request->dosen_id));
        }

        if ($request->filled('tahun_ajaran_id')) {
            $query->whereHas('kelasPerkuliahan', fn($q) => $q->where('tahun_ajaran_id', $request->tahun_ajaran_id));
        }

        $query->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')")->orderBy('jam_mulai');

        $perPage      = $request->input('per_page', 10);
        $jadwals      = $query->paginate($perPage)->withQueryString();
        $kelases      = KelasPerkuliahan::with(['mataKuliah', 'dosen'])->get();
        $ruangans     = Ruangan::orderBy('nama_ruangan')->get();
        $dosens       = Dosen::whereHas('kelasPerkuliahan.jadwalPerkuliahans')->orderBy('nama_dosen')->get();
        $tahunAjarans = TahunAjaran::orderByDesc('tahun_ajaran')->get();

        return view('admin.jadwal.index', compact('jadwals', 'kelases', 'ruangans', 'dosens', 'tahunAjarans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_perkuliahan_id' => 'required|exists:kelas_perkuliahans,id',
            'ruangan_id'           => 'required|exists:ruangans,id',
            'hari'                 => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai'            => 'required',
            'jam_selesai'          => 'required|after:jam_mulai',
        ]);

        try {
            JadwalPerkuliahan::create($validated);
            return redirect()->back()->with('success', 'Jadwal perkuliahan berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Gagal: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        // Cari jadwal yang mau diedit
        $jadwal = \App\Models\JadwalPerkuliahan::findOrFail($id);

        // Simpan perubahan data dari form Pop-up
        $jadwal->update([
            'kelas_perkuliahan_id' => $request->kelas_perkuliahan_id,
            'ruangan_id' => $request->ruangan_id,
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
        ]);

        // Kembalikan ke halaman jadwal dengan pesan sukses
        return redirect()->back()->with('success', 'Jadwal berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $jadwal = JadwalPerkuliahan::findOrFail($id);
        $jadwal->delete();

        return redirect()->back()->with('success', 'Jadwal berhasil dihapus!');
    }
}

