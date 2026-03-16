<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');

            // Paksa semua request menggunakan IPv4
            if (isset($_SERVER['REMOTE_ADDR'])) {
                $_SERVER['REMOTE_ADDR'] = preg_replace('/^::ffff:/', '', $_SERVER['REMOTE_ADDR']);
            }
        }
    }
}
