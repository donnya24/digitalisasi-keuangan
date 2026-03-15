<?php
// app/Http/Middleware/HttpsProtocol.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest; // Import ini!

class HttpsProtocol
{
    public function handle(Request $request, Closure $next)
    {
        // FORCE HTTPS di production
        if (!$request->secure() && App::environment('production')) {
            return redirect()->secure($request->getRequestUri());
        }

        // Set trusted proxies untuk Railway - gunakan SymfonyRequest::HEADER_*
        $request->setTrustedProxies(
            ['*'],
            SymfonyRequest::HEADER_X_FORWARDED_FOR |
            SymfonyRequest::HEADER_X_FORWARDED_HOST |
            SymfonyRequest::HEADER_X_FORWARDED_PORT |
            SymfonyRequest::HEADER_X_FORWARDED_PROTO
        );

        return $next($request);
    }
}
