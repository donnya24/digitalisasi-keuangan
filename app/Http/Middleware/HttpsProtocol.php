<?php
// app/Http/Middleware/HttpsProtocol.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class HttpsProtocol
{
    public function handle(Request $request, Closure $next)
    {
        // FORCE HTTPS di semua environment
        if (!$request->secure() && App::environment('production')) {
            return redirect()->secure($request->getRequestUri());
        }

        // Set trusted proxies untuk Railway
        $request->setTrustedProxies(
            ['*'], 
            Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT |
            Request::HEADER_X_FORWARDED_PROTO
        );

        return $next($request);
    }
}