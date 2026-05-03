<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PresensiApiController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        // TODO: validasi GPS, foto selfie, simpan record Presensi
        return response()->json([
            'status'  => true,
            'message' => 'Presensi berhasil direkam. (belum diimplementasi)',
            'data'    => null,
        ]);
    }

    public function riwayat(Request $request): JsonResponse
    {
        // TODO: kembalikan daftar Presensi milik mahasiswa yang login
        return response()->json([
            'status'  => true,
            'message' => 'Riwayat presensi. (belum diimplementasi)',
            'data'    => [],
        ]);
    }
}
