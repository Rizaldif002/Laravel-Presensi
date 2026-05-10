<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDosenRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'dosen') {
            return $next($request);
        }

        if ($user?->isAdmin()) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Halaman ini hanya dapat diakses oleh dosen.');
        }

        return redirect()
            ->route('login')
            ->with('error', 'Silakan login sebagai dosen.');
    }
}
