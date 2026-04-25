<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Http\Request; 

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Pengaturan middleware asli kamu tetap aman di sini
        $middleware->alias([
            'karyawan' => App\Http\Middleware\Karyawan::class,
            'login-karyawan' => App\Http\Middleware\LoginKaryawan::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Tangkap error 419 (CSRF Token Mismatch) dan jadikan elegan
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            return redirect()->back()
                ->withInput($request->except('password', '_token'))
                ->withErrors(['error' => 'Sesi halaman telah berakhir karena terlalu lama diam. Silakan coba klik tombol simpan/login sekali lagi.']);
        });
    })->create();