<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (Auth::check()) {
                try {
                    $user = Auth::user();

                    // ✅ BENAR: Query langsung ke model Notification
                    $notifications = \App\Models\Notification::where('user_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->take(5)
                        ->get()
                        ->map(function ($notification) {
                            return [
                                'id' => $notification->id,
                                'title' => $notification->title ?? 'Notifikasi',
                                'message' => $notification->message ?? '',
                                'time' => $notification->formatted_time, // PAKAI ACCESSOR
                                'is_read' => $notification->is_read === 'read', // boolean untuk Alpine
                                'icon' => $notification->icon, // PAKAI ACCESSOR
                                'text_color' => $notification->text_color, // PAKAI ACCESSOR
                                'bg_color' => $notification->bg_color, // PAKAI ACCESSOR
                                'type' => $notification->type,
                            ];
                        })->toArray();

                    // ✅ HITUNG UNREAD
                    $unreadCount = \App\Models\Notification::where('user_id', $user->id)
                        ->where('is_read', 'unread')
                        ->count();

                    // DEBUG LOG
                    Log::info('VIEW COMPOSER: Notifikasi ditemukan', [
                        'user_id' => $user->id,
                        'count' => count($notifications),
                        'unread' => $unreadCount,
                        'path' => request()->path()
                    ]);

                    $view->with([
                        'shared_notifications' => $notifications,
                        'shared_unread_count' => $unreadCount,
                    ]);

                } catch (\Exception $e) {
                    Log::error('VIEW COMPOSER ERROR: ' . $e->getMessage(), [
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]);

                    $view->with([
                        'shared_notifications' => [],
                        'shared_unread_count' => 0,
                    ]);
                }
            } else {
                $view->with([
                    'shared_notifications' => [],
                    'shared_unread_count' => 0,
                ]);
            }
        });
    }
}
