<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MataKuliahImport;

class MataKuliahController extends Controller
{
    /**
     * Menampilkan daftar mata kuliah dengan fitur pencarian dan filter semester.
     */
    public function index(Request $request)
    {
        $query = MataKuliah::query();

        // Pencarian berdasarkan nama atau kode mata kuliah
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_mk', 'like', '%' . $search . '%')
                  ->orWhere('kode_mk', 'like', '%' . $search . '%');
            });
        }

        // Filter berdasarkan semester
        if ($request->filled('semester')) {
            $semester = $request->input('semester');
            $query->where('semester', $semester);
        }

        $mataKuliahs = $query->get();

        return view('admin.mata-kuliah.index', compact('mataKuliahs'));
    }

    /**
     * Menyimpan mata kuliah baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_mk'   => 'required|unique:mata_kuliahs,kode_mk',
            'nama_mk'   => 'required',
            'sks'       => 'required|numeric',
            'semester'  => 'required|numeric',
        ], [
            'kode_mk.unique'     => 'Kode Mata Kuliah ini sudah terdaftar! Silakan gunakan kode lain.',
            'kode_mk.required'   => 'Kode MK tidak boleh kosong.',
            'nama_mk.required'   => 'Nama Mata Kuliah tidak boleh kosong.',
            'sks.required'       => 'SKS tidak boleh kosong.',
            'semester.required'  => 'Semester tidak boleh kosong.',
        ]);

        MataKuliah::create($validated);

        return redirect()->back()->with('success', 'Mata Kuliah berhasil ditambahkan!');
    }

    /**
     * Memperbarui data mata kuliah berdasarkan id.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'kode_mk'   => 'required|unique:mata_kuliahs,kode_mk,' . $id,
            'nama_mk'   => 'required',
            'sks'       => 'required|numeric',
            'semester'  => 'required|numeric',
        ], [
            'kode_mk.unique'     => 'Kode Mata Kuliah ini sudah terdaftar! Silakan gunakan kode lain.',
            'kode_mk.required'   => 'Kode MK tidak boleh kosong.',
            'nama_mk.required'   => 'Nama Mata Kuliah tidak boleh kosong.',
            'sks.required'       => 'SKS tidak boleh kosong.',
            'semester.required'  => 'Semester tidak boleh kosong.',
        ]);

        $mataKuliah = MataKuliah::findOrFail($id);
        $mataKuliah->update($validated);

        return redirect()->back()->with('success', 'Mata Kuliah berhasil diperbarui!');
    }

    /**
     * Menghapus data mata kuliah berdasarkan id.
     */
    public function destroy($id)
    {
        $mataKuliah = MataKuliah::findOrFail($id);
        $mataKuliah->delete();

        return redirect()->back()->with('success', 'Mata Kuliah berhasil dihapus!');
    }

    /**
     * Import data mata kuliah via file excel
     */
    public function import(Request $request) 
    {
        // Gunakan 'file' agar seragam dengan yang biasanya ada di Blade
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ], [
            'file.required' => 'File Excel harus diunggah.',
            'file.mimes' => 'Format file harus .xlsx, .xls, atau .csv'
        ]);
    
        try {
            Excel::import(new MataKuliahImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data Mata Kuliah berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Gagal: ' . $e->getMessage()]);
        }
    }
}
