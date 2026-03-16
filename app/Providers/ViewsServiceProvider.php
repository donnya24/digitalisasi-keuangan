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
                    $userId = Auth::id();

                    // 🌟 AMBIL NOTIFIKASI PERSIS SEPERTI DI DASHBOARD
                    $notifications = Notification::where('user_id', $userId)
                        ->where('is_read', 'unread')  // FILTER UNREAD (sesuai dashboard)
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get()
                        ->map(function ($notification) {
                            return [
                                'id' => $notification->id,
                                'type' => $notification->type,
                                'icon' => $notification->icon ?? 'bell',
                                'title' => $notification->title ?? 'Notifikasi',
                                'message' => $notification->message ?? '',
                                'time' => $notification->formatted_time ?? $notification->created_at->diffForHumans(),
                                'bg_color' => $notification->bg_color ?? 'bg-gray-100',
                                'text_color' => $notification->text_color ?? 'text-gray-600',
                                'is_read' => $notification->is_read === 'read',
                            ];
                        })->toArray();

                    // HITUNG UNREAD
                    $unreadCount = Notification::where('user_id', $userId)
                        ->where('is_read', 'unread')
                        ->count();

                    // 🔍 DEBUG LENGKAP
                    Log::info('🔔 VIEW COMPOSER NOTIF', [
                        'user_id' => $userId,
                        'query' => "where user_id = $userId AND is_read = 'unread'",
                        'result_count' => count($notifications),
                        'unread_count' => $unreadCount,
                        'first_notif' => $notifications[0] ?? null
                    ]);

                    $view->with([
                        'shared_notifications' => $notifications,
                        'shared_unread_count' => $unreadCount,
                    ]);

                } catch (\Exception $e) {
                    Log::error('❌ VIEW COMPOSER ERROR: ' . $e->getMessage(), [
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTraceAsString()
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
