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
        // 1. Ambil data jadwal dengan relasi lengkap
        $query = JadwalPerkuliahan::with(['kelasPerkuliahan.mataKuliah', 'kelasPerkuliahan.dosen', 'ruangan'])->latest();

        // 2. Fitur Pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('kelasPerkuliahan.mataKuliah', function ($q) use ($search) {
                $q->where('nama_mk', 'like', '%' . $search . '%');
            })->orWhereHas('kelasPerkuliahan.dosen', function ($q) use ($search) {
                $q->where('nama_dosen', 'like', '%' . $search . '%');
            })->orWhere('hari', 'like', '%' . $search . '%');
        }

        $jadwals = $query->get();

        // 3. Data Master untuk Modal Tambah/Edit
        $kelases = KelasPerkuliahan::with(['mataKuliah', 'dosen'])->get();
        $ruangans = Ruangan::all();

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

