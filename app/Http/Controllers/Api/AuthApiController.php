<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'nim'      => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('nim', $request->nim)
            ->where('role', 'mahasiswa')
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'nim' => ['NIM atau password salah, atau akun bukan mahasiswa.'],
            ]);
        }

        $token = $user->createToken('mahasiswa-app')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'token' => $token,
                'user'  => [
                    'id'   => $user->id,
                    'name' => $user->name,
                    'nim'  => $user->nim,
                    'role' => $user->role,
                ],
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Logout berhasil.',
            'data'    => null,
        ]);
    }

    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'status'  => true,
            'message' => 'Data profil.',
            'data'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'nim'   => $user->nim,
                'role'  => $user->role,
            ],
        ]);
    }
}
