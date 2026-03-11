<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\Notification;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL; // tambahkan ini

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Force HTTPS (penting jika menggunakan ngrok / production HTTPS)
        if (app()->environment('local')) {
            URL::forceScheme('https');
        }

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->input('email').'|'.$request->ip());
        });

        // Menggunakan view composer untuk layout app
        View::composer('components.layout.app', function ($view) {
            if (Auth::check()) {
                $userId = Auth::id();

                // Ambil notifikasi terbaru
                $notifications = Notification::where('user_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get()
                    ->map(function ($notification) {
                        return [
                            'id' => $notification->id,
                            'type' => $notification->type,
                            'icon' => $notification->icon,
                            'title' => $notification->title,
                            'message' => $notification->message,
                            'time' => $notification->formatted_time,
                            'bg_color' => $notification->bg_color,
                            'text_color' => $notification->text_color,
                            'is_read' => $notification->is_read === 'read',
                        ];
                    });

                $unreadNotifications = Notification::where('user_id', $userId)
                    ->where('is_read', 'unread')
                    ->count();

                $view->with([
                    'notifications' => $notifications,
                    'unreadNotifications' => $unreadNotifications,
                ]);
            } else {
                $view->with([
                    'notifications' => collect(),
                    'unreadNotifications' => 0,
                ]);
            }
        });
    }
}