<?php
// app/Providers/ViewServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Share ke SEMUA view - TANPA PENGECUALIAN!
        View::composer('*', function ($view) {
            // Cek apakah user login
            if (Auth::check()) {
                $user = Auth::user();

                // AMBIL NOTIFIKASI - Pastikan query benar
                $notifications = $user->notifications()
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(function ($notification) {
                        $data = $notification->data;
                        return [
                            'id' => $notification->id,
                            'title' => $data['title'] ?? 'Notifikasi',
                            'message' => $data['message'] ?? '',
                            'time' => $notification->created_at->diffForHumans(),
                            'is_read' => !is_null($notification->read_at),
                            'icon' => $data['icon'] ?? 'bell',
                            'text_color' => $data['text_color'] ?? 'text-gray-600',
                            'bg_color' => $data['bg_color'] ?? 'bg-gray-100',
                        ];
                    })->toArray();

                $unreadCount = $user->unreadNotifications->count();

                // FORCE SHARE - gunakan nama yang SAMA PERSIS
                $view->with([
                    'shared_notifications' => $notifications,
                    'shared_unread_count' => $unreadCount,
                ]);

                // DEBUG: Tulis ke log untuk memastikan
                \Log::info('ViewComposer: Notifikasi dishare', [
                    'path' => request()->path(),
                    'count' => count($notifications),
                    'unread' => $unreadCount
                ]);
            } else {
                $view->with([
                    'shared_notifications' => [],
                    'shared_unread_count' => 0,
                ]);
            }
        });
    }

    public function register(): void
    {
        //
    }
}
