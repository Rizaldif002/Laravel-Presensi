<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ruangan;
use Illuminate\Http\Request;

class RuanganController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = Ruangan::query();

        if ($request->filled('search')) {
            $query->where('nama_ruangan', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('radius_min')) {
            $query->where('radius_meter', '>=', (int) $request->radius_min);
        }

        if ($request->filled('radius_max')) {
            $query->where('radius_meter', '<=', (int) $request->radius_max);
        }

        $ruangans = $query->orderBy('nama_ruangan')->paginate($perPage)->withQueryString();
        return view('admin.ruangan.index', compact('ruangans'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_ruangan'   => 'required',
            'latitude'       => 'required',
            'longitude'      => 'required',
            'radius_meter'   => 'required|numeric|min:5', // Minimal radius 5 meter
        ], [
            'nama_ruangan.required' => 'Nama Ruangan harus diisi.',
            'latitude.required'     => 'Titik Latitude belum dipilih di peta.',
            'longitude.required'    => 'Titik Longitude belum dipilih di peta.',
            'radius_meter.required' => 'Radius harus diisi.',
            'radius_meter.numeric'  => 'Radius harus berupa angka.',
            'radius_meter.min'      => 'Radius minimal 5 meter.',
        ]);

        Ruangan::create($validatedData);

        return redirect()->back()->with('success', 'Data Ruangan & Titik Koordinat berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nama_ruangan'   => 'required',
            'latitude'       => 'required',
            'longitude'      => 'required',
            'radius_meter'   => 'required|numeric|min:5',
        ], [
            'nama_ruangan.required' => 'Nama Ruangan harus diisi.',
            'latitude.required'     => 'Titik lokasi harus dipilih pada peta.',
            'longitude.required'    => 'Titik Longitude belum dipilih di peta.',
            'radius_meter.required' => 'Radius harus diisi.',
            'radius_meter.numeric'  => 'Radius harus berupa angka.',
            'radius_meter.min'      => 'Radius minimal 5 meter.',
        ]);

        $ruangan = Ruangan::findOrFail($id);

        $ruangan->update($validatedData);

        return redirect()->back()->with('success', 'Lokasi ruangan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Ruangan::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data Ruangan berhasil dihapus!');
    }
}

