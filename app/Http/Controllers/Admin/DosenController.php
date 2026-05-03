<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\DosenImport;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DosenController extends Controller
{
    /**
     * Menampilkan daftar dosen dengan fitur pencarian dan pengurutan
     */
    public function index(Request $request)
    {
        $query = Dosen::query();

        // Fitur pencarian
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nip', 'like', '%' . $search . '%')
                    ->orWhere('nama_dosen', 'like', '%' . $search . '%');
            });
        }

        // Fitur pengurutan
        if ($request->filled('sort')) {
            if ($request->input('sort') === 'nip_asc') {
                $query->orderBy('nip', 'asc');
            } elseif ($request->input('sort') === 'nip_desc') {
                $query->orderBy('nip', 'desc');
            }
        } else {
            // Default urut dari NIP terkecil
            $query->orderBy('nip', 'asc');
        }

        $dosens = $query->get();

        return view('admin.dosen.index', compact('dosens'));
    }

    /**
     * Menyimpan data dosen baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip'        => 'required|unique:dosens,nip',
            'nama_dosen' => 'required',
            'no_hp'      => 'nullable',
        ], [
            'nip.required'        => 'NIP wajib diisi.',
            'nip.unique'          => 'NIP ini sudah terdaftar!',
            'nama_dosen.required' => 'Nama Dosen wajib diisi.',
        ]);

        // Simpan data dosen
        Dosen::create($validated);

        return redirect()->back()->with('success', 'Data Dosen berhasil ditambahkan!');
    }

    /**
     * Update data dosen berdasarkan ID
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nip'        => 'required|unique:dosens,nip,' . $id,
            'nama_dosen' => 'required',
            'no_hp'      => 'nullable',
        ], [
            'nip.required'        => 'NIP wajib diisi.',
            'nip.unique'          => 'NIP ini sudah terdaftar!',
            'nama_dosen.required' => 'Nama Dosen wajib diisi.',
        ]);

        $dosen = Dosen::findOrFail($id);
        $dosen->update($validated);

        return redirect()->back()->with('success', 'Data Dosen berhasil diperbarui!');
    }

    /**
     * Menghapus data dosen berdasarkan ID
     */
    public function destroy($id)
    {
        $dosen = Dosen::findOrFail($id);
        $dosen->delete();

        return redirect()->back()->with('success', 'Data Dosen berhasil dihapus!');
    }

    public function import(Request $request)
    {
        // Ubah 'file_excel' menjadi 'file' agar sesuai dengan Blade
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            // Gunakan nama yang sama di sini
            Excel::import(new DosenImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data Dosen berhasil di-import!');
        } catch (\Exception $e) {
            // Opsional: tampilkan pesan error asli untuk debugging jika gagal
            return redirect()->back()->withErrors(['Format Excel Dosen tidak sesuai: ' . $e->getMessage()]);
        }
    }
}

