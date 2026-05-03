<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\KelasImport;
use App\Models\Dosen;
use App\Models\KelasPerkuliahan;
use App\Models\MataKuliah;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class KelasPerkuliahanController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        try {
            Excel::import(new KelasImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data Kelas berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Gagal import: Pastikan nama Matkul/Dosen sesuai data master.']);
        }
    }

    public function index(Request $request)
    {
        // 1. Siapkan Query Dasar (Relasi 'ruangan' dihapus)
        $query = KelasPerkuliahan::with(['tahunAjaran', 'mataKuliah', 'dosen'])->latest();

        // 2. Fitur Pencarian (Search)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kelas', 'like', '%' . $search . '%')
                    ->orWhereHas('mataKuliah', function ($q2) use ($search) {
                        $q2->where('nama_mk', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('dosen', function ($q2) use ($search) {
                        $q2->where('nama_dosen', 'like', '%' . $search . '%');
                    });
            });
        }

        // Eksekusi Query
        $kelas = $query->get();

        // Data Master untuk Modal Tambah & Edit (Query Ruangan dihapus)
        $tahunAjarans = TahunAjaran::all();
        $mataKuliahs  = MataKuliah::all();
        $dosens       = Dosen::all();

        return view('admin.kelas.index', compact('kelas', 'tahunAjarans', 'mataKuliahs', 'dosens'));
    }

    public function store(Request $request)
    {
        // Validasi dirampingkan, hanya fokus pada pembuatan Wadah Kelas
        $validatedData = $request->validate([
            'tahun_ajaran_id' => 'required',
            'mata_kuliah_id'  => 'required',
            'dosen_id'        => 'required',
            'nama_kelas'      => 'required|string|max:50',
        ]);

        try {
            KelasPerkuliahan::create($validatedData);
            return redirect()->back()->with('success', 'Wadah Kelas Perkuliahan berhasil dibuat!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        // Validasi dirampingkan
        $validatedData = $request->validate([
            'tahun_ajaran_id' => 'required',
            'mata_kuliah_id'  => 'required',
            'dosen_id'        => 'required',
            'nama_kelas'      => 'required|string|max:50',
        ]);

        try {
            $kelas = KelasPerkuliahan::findOrFail($id);
            $kelas->update($validatedData);
            return redirect()->back()->with('success', 'Data Kelas berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $kelas = KelasPerkuliahan::findOrFail($id);
            $kelas->delete();
            return redirect()->back()->with('success', 'Kelas berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}

