<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\PesertaKelasImport;
use App\Models\KelasPerkuliahan;
use App\Models\Mahasiswa;
use App\Models\PesertaKelas;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PesertaKelasController extends Controller
{
    public function index(Request $request, KelasPerkuliahan $kelas)
    {
        $kelas->load(['mataKuliah', 'dosen', 'tahunAjaran']);

        $query = PesertaKelas::where('peserta_kelas.kelas_perkuliahan_id', $kelas->id)
            ->join('mahasiswas', 'peserta_kelas.mahasiswa_id', '=', 'mahasiswas.id')
            ->select('peserta_kelas.*');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('mahasiswas.nim', 'like', "%{$search}%")
                  ->orWhere('mahasiswas.nama_lengkap', 'like', "%{$search}%");
            });
        }

        $sortMap = [
            'nim_asc'   => ['mahasiswas.nim', 'asc'],
            'nim_desc'  => ['mahasiswas.nim', 'desc'],
            'nama_asc'  => ['mahasiswas.nama_lengkap', 'asc'],
            'nama_desc' => ['mahasiswas.nama_lengkap', 'desc'],
        ];
        [$sortCol, $sortDir] = $sortMap[$request->input('sort', 'nim_asc')] ?? ['mahasiswas.nim', 'asc'];
        $query->orderBy($sortCol, $sortDir);

        $perPage = $request->input('per_page', 15);
        $peserta = $query->with('mahasiswa')->paginate($perPage)->withQueryString();

        $mahasiswas = Mahasiswa::orderBy('nim')->get();

        return view('admin.kelas.peserta.index', compact('kelas', 'peserta', 'mahasiswas'));
    }

    public function store(Request $request, KelasPerkuliahan $kelas)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswas,id',
        ]);

        $sudahAda = PesertaKelas::where('kelas_perkuliahan_id', $kelas->id)
            ->where('mahasiswa_id', $request->mahasiswa_id)
            ->exists();

        if ($sudahAda) {
            return back()->withErrors(['Mahasiswa tersebut sudah terdaftar di kelas ini.']);
        }

        PesertaKelas::create([
            'kelas_perkuliahan_id' => $kelas->id,
            'mahasiswa_id'         => $request->mahasiswa_id,
        ]);

        return back()->with('success', 'Mahasiswa berhasil ditambahkan ke kelas.');
    }

    public function destroy(KelasPerkuliahan $kelas, int $peserta_id)
    {
        PesertaKelas::where('kelas_perkuliahan_id', $kelas->id)
            ->where('id', $peserta_id)
            ->delete();

        return back()->with('success', 'Mahasiswa berhasil dikeluarkan dari kelas.');
    }

    public function import(Request $request, KelasPerkuliahan $kelas)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new PesertaKelasImport($kelas->id), $request->file('file'));
            return back()->with('success', 'Import peserta berhasil.');
        } catch (\Exception $e) {
            return back()->withErrors(['Gagal import: Pastikan format file dan NIM mahasiswa sudah benar.']);
        }
    }
}
