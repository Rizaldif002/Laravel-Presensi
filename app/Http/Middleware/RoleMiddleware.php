<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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

        $userRole = auth()->user()->role;

        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // Smart redirect — arahkan ke halaman sesuai role user
        return match ($userRole) {
            'dosen' => redirect()->route('dosen.sesi.index')
                ->with('error', 'Halaman tersebut hanya bisa diakses oleh Administrator.'),
            'admin' => redirect()->route('dashboard')
                ->with('error', 'Halaman tersebut bukan untuk Administrator.'),
            default => redirect()->route('login')
                ->with('error', 'Akses ditolak. Silakan login kembali.'),
        };
    }
}
