<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    public function index(Request $request)
    {
        $query = TahunAjaran::query();

        // Fitur Filter: Jika ada input tahun_ajaran atau semester dari dropdown filter
        if ($request->filled('tahun_ajaran')) {
            $query->where('tahun_ajaran', $request->tahun_ajaran);
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        $tahunAjarans = $query->orderBy('tahun_ajaran', 'desc')->get();

        // Mengambil daftar tahun ajaran yang unik dari database untuk pilihan di filter
        $listTahun = TahunAjaran::select('tahun_ajaran')->distinct()->pluck('tahun_ajaran');

        return view('admin.tahun_ajaran.index', compact('tahunAjarans', 'listTahun'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran' => 'required',
            'semester'     => 'required|in:Ganjil,Genap'
        ]);

        // Cek apakah kombinasi tahun dan semester ini sudah ada
        $exists = TahunAjaran::where('tahun_ajaran', $request->tahun_ajaran)
            ->where('semester', $request->semester)
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['Data Tahun Ajaran dan Semester ini sudah ada!']);
        }

        // Jika ini data pertama, otomatis aktifkan
        $isActive = TahunAjaran::count() == 0 ? true : false;

        TahunAjaran::create([
            'tahun_ajaran' => $request->tahun_ajaran,
            'semester'     => $request->semester,
            'is_active'    => $isActive
        ]);

        return redirect()->back()->with('success', 'Tahun Ajaran berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tahun_ajaran' => 'required',
            'semester'     => 'required|in:Ganjil,Genap'
        ]);

        // Cek duplikasi data pada baris lain
        $exists = TahunAjaran::where('tahun_ajaran', $request->tahun_ajaran)
            ->where('semester', $request->semester)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['Data Tahun Ajaran dan Semester ini sudah ada!']);
        }

        TahunAjaran::findOrFail($id)->update([
            'tahun_ajaran' => $request->tahun_ajaran,
            'semester'     => $request->semester
        ]);

        return redirect()->back()->with('success', 'Tahun Ajaran berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);

        if ($tahunAjaran->is_active) {
            return redirect()->back()->withErrors(['Tidak bisa menghapus Tahun Ajaran yang sedang Aktif!']);
        }

        $tahunAjaran->delete();
        return redirect()->back()->with('success', 'Tahun Ajaran berhasil dihapus!');
    }

    public function setAktif($id)
    {
        TahunAjaran::query()->update(['is_active' => false]);
        TahunAjaran::findOrFail($id)->update(['is_active' => true]);
        return redirect()->back()->with('success', 'Tahun Ajaran Aktif berhasil diubah!');
    }
}

