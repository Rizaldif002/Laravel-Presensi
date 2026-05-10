<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: middleware('role:admin') or middleware('role:admin,dosen')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $userRole = $user->role;

        if (in_array($userRole, $roles, true)) {
            return $next($request);
        }

        if ($user->isMahasiswa()) {
            return redirect()
                ->route('home')
                ->with('error', 'Akses ditolak. Mahasiswa tidak memiliki halaman web ini.');
        }

        // Arahkan ke area yang sesuai peran (admin tidak ke URL dosen, sebaliknya).
        if ($user->isDosen()) {
            return redirect()
                ->route('dosen.sesi.index')
                ->with('error', 'Halaman tersebut hanya untuk Administrator.');
        }

        if ($user->isAdmin()) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Halaman tersebut hanya untuk Dosen.');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('error', 'Akses ditolak. Silakan login kembali.');
    }
}
