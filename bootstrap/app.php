<?php

use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // KEAMANAN: percaya semua proxy (Wasmer/Cloudflare dkk.) supaya
        // HTTPS terdeteksi benar dan header X-Forwarded-* tidak dipalsukan.
        $middleware->trustProxies(at: '*');

        // role: cek role user sebelum masuk route admin/staff
        $middleware->alias([
            'role' => CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
