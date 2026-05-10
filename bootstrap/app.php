<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectUsersTo(function (Request $request) {
            $user = $request->user();
            if ($user?->isDosen()) {
                return route('dosen.sesi.index');
            }
            if ($user?->isAdmin()) {
                return route('dashboard');
            }

            return route('home');
        });

        $middleware->alias([
            'role.dosen' => App\Http\Middleware\EnsureDosenRole::class,
            'role'       => App\Http\Middleware\RoleMiddleware::class,
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