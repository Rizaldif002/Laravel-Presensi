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
        $query = KelasPerkuliahan::with(['tahunAjaran', 'mataKuliah', 'dosen'])
            ->selectRaw('kelas_perkuliahans.*, (
                SELECT COUNT(*) FROM peserta_kelas
                WHERE peserta_kelas.kelas_perkuliahan_id = kelas_perkuliahans.id
            ) as peserta_count')
            ->latest('kelas_perkuliahans.created_at');

        if ($request->filled('tahun_ajaran_id')) {
            $query->where('tahun_ajaran_id', $request->tahun_ajaran_id);
        }

        if ($request->filled('dosen_id')) {
            $query->where('dosen_id', $request->dosen_id);
        }

        if ($request->filled('mata_kuliah_id')) {
            $query->where('mata_kuliah_id', $request->mata_kuliah_id);
        }

        $perPage  = $request->input('per_page', 10);
        $kelas    = $query->paginate($perPage)->withQueryString();

        $tahunAjarans = TahunAjaran::orderByDesc('tahun_ajaran')->get();
        $mataKuliahs  = MataKuliah::all();
        $dosens       = Dosen::orderBy('nama_dosen')->get();

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

