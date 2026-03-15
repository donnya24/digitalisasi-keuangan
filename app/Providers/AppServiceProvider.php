<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // PAKSA HTTPS - Metode 1
        URL::forceScheme('https');

        // PAKSA HTTPS - Metode 2 (override config)
        if ($this->app->environment('production')) {
            $this->app['request']->server->set('HTTPS', 'on');

            // Paksa session menggunakan HTTPS
            Config::set('session.secure', true);
            Config::set('session.same_site', 'none'); // Ganti ke 'none' untuk cross-site
            Config::set('session.domain', '.railway.app');

            // Paksa cookie menggunakan HTTPS
            Config::set('app.url', 'https://digitalisasi-keuangan-production.up.railway.app');
            Config::set('app.asset_url', 'https://digitalisasi-keuangan-production.up.railway.app');
        }
    }
}
