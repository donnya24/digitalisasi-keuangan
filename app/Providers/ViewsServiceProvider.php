<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Notification;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (Auth::check()) {
                try {
                    $user = Auth::user();

                    // 🔥 QUERY PERSIS SEPERTI DEBUG YANG BERHASIL
                    $notifications = Notification::where('user_id', $user->id)
                        ->latest() // sama dengan orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get()
                        ->map(function ($notification) {
                            // PAKAI ACCESSORS DARI MODEL
                            return [
                                'id' => $notification->id,
                                'title' => $notification->title,
                                'message' => $notification->message,
                                'time' => $notification->formatted_time,
                                'icon' => $notification->icon,
                                'bg_color' => $notification->bg_color,
                                'text_color' => $notification->text_color,
                                'is_read' => $notification->isRead(), // boolean
                                'type' => $notification->type,
                            ];
                        })->toArray();

                    // HITUNG UNREAD PAKAI SCOPE
                    $unreadCount = Notification::where('user_id', $user->id)
                        ->unread()
                        ->count();

                    // FORCE SHARE
                    $view->with([
                        'shared_notifications' => $notifications,
                        'shared_unread_count' => $unreadCount,
                    ]);

                    // DEBUG DI HTML - PASTIKAN KELUAR
                    echo "<!-- VIEW COMPOSER SUCCESS: count=" . count($notifications) . " unread=" . $unreadCount . " -->";

                } catch (\Exception $e) {
                    // TULIS ERROR KE HTML
                    echo "<!-- VIEW COMPOSER ERROR: " . $e->getMessage() . " -->";

                    Log::error('VIEW COMPOSER ERROR: ' . $e->getMessage());

                    $view->with([
                        'shared_notifications' => [],
                        'shared_unread_count' => 0,
                    ]);
                }
            }
        });
    }
}
