<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDosenRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role === 'dosen') {
            return $next($request);
        }

        return redirect()->route('dashboard')
            ->with('error', 'Halaman ini hanya dapat diakses oleh dosen.');
    }
}
