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
        // Share ke SEMUA view
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();

                // Ambil 5 notifikasi terbaru
                $notifications = $user->notifications()
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(function ($notification) {
                        return [
                            'id' => $notification->id,
                            'title' => $notification->data['title'] ?? 'Notifikasi',
                            'message' => $notification->data['message'] ?? '',
                            'time' => $notification->created_at->diffForHumans(),
                            'is_read' => !is_null($notification->read_at),
                            'icon' => $notification->data['icon'] ?? 'bell',
                            'text_color' => $notification->data['text_color'] ?? 'text-gray-600',
                            'bg_color' => $notification->data['bg_color'] ?? 'bg-gray-100',
                        ];
                    })->toArray();

                $unreadCount = $user->unreadNotifications->count();
            } else {
                $notifications = [];
                $unreadCount = 0;
            }

            // Share ke SEMUA view dengan nama yang konsisten
            $view->with([
                'shared_notifications' => $notifications,
                'shared_unread_count' => $unreadCount,
            ]);
        });
    }

    public function register(): void
    {
        //
    }
}
