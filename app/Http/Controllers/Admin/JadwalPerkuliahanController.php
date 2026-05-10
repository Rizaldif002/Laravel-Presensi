<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalPerkuliahan;
use App\Models\KelasPerkuliahan;
use App\Models\Ruangan;
use Illuminate\Http\Request;

class JadwalPerkuliahanController extends Controller
{
    public function index(Request $request)
    {
        $query = JadwalPerkuliahan::with(['kelasPerkuliahan.mataKuliah', 'kelasPerkuliahan.dosen', 'ruangan']);

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

        $hariOrder = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7];
        $query->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')")->orderBy('jam_mulai');

        $perPage = $request->input('per_page', 10);
        $jadwals = $query->paginate($perPage)->withQueryString();

        $kelases  = KelasPerkuliahan::with(['mataKuliah', 'dosen'])->get();
        $ruangans = Ruangan::orderBy('nama_ruangan')->get();

        return view('admin.jadwal.index', compact('jadwals', 'kelases', 'ruangans'));
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

