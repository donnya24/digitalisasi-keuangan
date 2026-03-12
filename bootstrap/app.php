<?php
// bootstrap/app.php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Trust semua proxies (WAJIB UNTUK RAILWAY)
        $middleware->trustProxies(at: '*');
        
        // Tambahkan middleware ke grup web
        $middleware->web(append: [
            \App\Http\Middleware\HttpsProtocol::class, // Akan kita buat
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();