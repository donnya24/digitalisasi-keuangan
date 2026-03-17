<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Notification; // <-- PASTIKAN IMPORT INI!

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (Auth::check()) {
                try {
                    $user = Auth::user();

                    // 🔥 PAKAI MODEL DENGAN SCOPES YANG SUDAH ADA
                    $notifications = Notification::where('user_id', $user->id)
                        ->whereIn('is_read', ['unread', 'read']) // Hanya yang aktif
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get()
                        ->map(function ($notification) {
                            // GUNAKAN ACCESSORS DARI MODEL
                            return [
                                'id' => $notification->id,
                                'title' => $notification->title,
                                'message' => $notification->message,
                                'time' => $notification->formatted_time, // PAKAI ACCESSOR
                                'icon' => $notification->icon, // PAKAI ACCESSOR
                                'bg_color' => $notification->bg_color, // PAKAI ACCESSOR
                                'text_color' => $notification->text_color, // PAKAI ACCESSOR
                                'is_read' => $notification->isRead(), // PAKAI METHOD isRead()
                                'type' => $notification->type,
                            ];
                        })->toArray();

                    // HITUNG UNREAD PAKAI SCOPE
                    $unreadCount = Notification::where('user_id', $user->id)
                        ->unread() // PAKAI SCOPE unread()
                        ->count();

                    // 🔍 DEBUG LENGKAP
                    Log::channel('stderr')->info('🔔 NOTIF DEBUG', [
                        'user_id' => $user->id,
                        'method' => 'ViewServiceProvider',
                        'total_notif' => count($notifications),
                        'unread_count' => $unreadCount,
                        'first_notif' => $notifications[0] ?? null
                    ]);

                    // FORCE SHARE
                    $view->with([
                        'shared_notifications' => $notifications,
                        'shared_unread_count' => $unreadCount,
                    ]);

                    // DEBUG DI HTML (HAPUS NANTI)
                    echo "<!-- NOTIF WORKS: count=" . count($notifications) . " unread=" . $unreadCount . " -->";

                } catch (\Exception $e) {
                    Log::error('❌ VIEW COMPOSER ERROR: ' . $e->getMessage(), [
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
