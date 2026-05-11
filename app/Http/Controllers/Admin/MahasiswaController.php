<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\MahasiswaImport;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class MahasiswaController extends Controller
{
    /**
     * Menampilkan daftar Mahasiswa dengan fitur pencarian dan urut.
     */
    public function index(Request $request)
    {
        $query = Mahasiswa::query();

        // Fitur pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                    ->orWhere('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('program_studi', 'like', "%{$search}%");
            });
        }

        // Fitur pengurutan
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'nim_asc':
                    $query->orderBy('nim', 'asc');
                    break;
                case 'nim_desc':
                    $query->orderBy('nim', 'desc');
                    break;
                default:
                    $query->orderBy('nim', 'asc');
            }
        } else {
            $query->orderBy('nim', 'asc');
        }

        $perPage = request('per_page', 10);
        $mahasiswas = $query->paginate($perPage)->withQueryString();

        return view('admin.mahasiswa.index', compact('mahasiswas'));
    }

    /**
     * Menyimpan data Mahasiswa baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nim'           => 'required|unique:mahasiswas,nim',
            'nama_lengkap'  => 'required',
            'program_studi' => 'required',
        ], [
            'nim.unique'              => 'NIM ini sudah terdaftar!',
            'nim.required'            => 'NIM wajib diisi.',
            'nama_lengkap.required'   => 'Nama lengkap wajib diisi.',
            'program_studi.required'  => 'Program Studi wajib diisi.',
        ]);

        Mahasiswa::create($request->only('nim', 'nama_lengkap', 'program_studi', 'no_hp'));

        return redirect()->back()->with('success', 'Data Mahasiswa berhasil ditambahkan!');
    }

    /**
     * Memperbarui data Mahasiswa.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nim'            => 'required|unique:mahasiswas,nim,' . $id,
            'nama_lengkap'   => 'required',
            'program_studi'  => 'required',
            'foto_referensi' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'nim.unique'             => 'NIM ini sudah terdaftar!',
            'nim.required'           => 'NIM wajib diisi.',
            'nama_lengkap.required'  => 'Nama lengkap wajib diisi.',
            'program_studi.required' => 'Program Studi wajib diisi.',
            'foto_referensi.image'   => 'File harus berupa gambar.',
            'foto_referensi.max'     => 'Ukuran foto maksimal 2MB.',
        ]);

        $mahasiswa = Mahasiswa::findOrFail($id);
        $data = $request->only('nim', 'nama_lengkap', 'program_studi', 'no_hp');

        if ($request->hasFile('foto_referensi')) {
            // Hapus foto lama jika ada
            if ($mahasiswa->foto_referensi) {
                Storage::disk('local')->delete($mahasiswa->foto_referensi);
            }
            $nim  = $request->nim ?: $mahasiswa->nim;
            $path = $request->file('foto_referensi')
                ->storeAs('wajah-referensi', $nim . '.jpg', 'local');
            $data['foto_referensi'] = $path;
        }

        $mahasiswa->update($data);

        return redirect()->back()->with('success', 'Data Mahasiswa berhasil diperbarui!');
    }

    /**
     * Menghapus data Mahasiswa.
     */
    public function destroy($id)
    {
        Mahasiswa::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data Mahasiswa berhasil dihapus!');
    }

    /**
     * Import data Mahasiswa dari file Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048'
        ], [
            'file_excel.required' => 'Pilih file Excel terlebih dahulu!',
            'file_excel.mimes'    => 'Format file harus berupa excel (.xlsx / .csv)!',
        ]);

        try {
            Excel::import(new MahasiswaImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data Mahasiswa berhasil di-import dari Excel!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Terjadi kesalahan pada format Excel Anda. Pastikan kolom sesuai urutan.']);
        }
    }
}

