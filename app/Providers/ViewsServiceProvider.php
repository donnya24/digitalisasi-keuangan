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

                    // AMBIL NOTIFIKASI - FILTER UNREAD DAN READ (TAPI TIDAK ARCHIVED)
                    $notifications = Notification::where('user_id', $userId)
                        ->whereIn('is_read', ['unread', 'read']) // Hanya yang aktif
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get()
                        ->map(function ($notification) {
                            return [
                                'id' => $notification->id,
                                'title' => $notification->title ?? 'Notifikasi',
                                'message' => $notification->message ?? '',
                                'time' => $notification->created_at->diffForHumans(),
                                'is_read' => $notification->is_read === 'read', // boolean untuk Alpine
                                'icon' => $notification->icon ?? $this->getIcon($notification->type),
                                'text_color' => $notification->text_color ?? $this->getTextColor($notification->type),
                                'bg_color' => $notification->bg_color ?? $this->getBgColor($notification->type),
                                'type' => $notification->type,
                            ];
                        })->toArray();

                    // HITUNG UNREAD - KHUSUS YANG 'unread'
                    $unreadCount = Notification::where('user_id', $userId)
                        ->where('is_read', 'unread')
                        ->count();

                    // DEBUG LOG
                    Log::info('NOTIF VIEW COMPOSER', [
                        'user_id' => $userId,
                        'total_notif' => count($notifications),
                        'unread_count' => $unreadCount,
                        'first_notif' => $notifications[0] ?? null
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

    private function getIcon($type)
    {
        $icons = [
            'profit_decrease' => 'exclamation-triangle',
            'profit_increase' => 'chart-line',
            'large_expense' => 'bell',
            'prive' => 'money-bill-wave',
            'low_balance' => 'exclamation-circle',
            'target_achieved' => 'trophy',
            'target_progress' => 'chart-pie',
            'no_transaction' => 'bell',
        ];
        return $icons[$type] ?? 'bell';
    }

    private function getTextColor($type)
    {
        $colors = [
            'profit_decrease' => 'text-red-600',
            'profit_increase' => 'text-green-600',
            'large_expense' => 'text-yellow-600',
            'prive' => 'text-purple-600',
            'low_balance' => 'text-yellow-600',
            'target_achieved' => 'text-green-600',
            'target_progress' => 'text-blue-600',
            'no_transaction' => 'text-blue-600',
        ];
        return $colors[$type] ?? 'text-gray-600';
    }

    private function getBgColor($type)
    {
        $colors = [
            'profit_decrease' => 'bg-red-50',
            'profit_increase' => 'bg-green-50',
            'large_expense' => 'bg-yellow-50',
            'prive' => 'bg-purple-50',
            'low_balance' => 'bg-yellow-50',
            'target_achieved' => 'bg-green-50',
            'target_progress' => 'bg-blue-50',
            'no_transaction' => 'bg-blue-50',
        ];
        return $colors[$type] ?? 'bg-gray-50';
    }
}
