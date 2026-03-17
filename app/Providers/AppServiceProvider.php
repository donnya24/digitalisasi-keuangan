<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // ========== HTTPS FORCE UNTUK PRODUCTION ==========
        if (app()->environment('production')) {
            URL::forceScheme('https');

            // Paksa semua request menggunakan IPv4
            if (isset($_SERVER['REMOTE_ADDR'])) {
                $_SERVER['REMOTE_ADDR'] = preg_replace('/^::ffff:/', '', $_SERVER['REMOTE_ADDR']);
            }
        }

        // ========== SHARE NOTIFIKASI KE SEMUA VIEW ==========
        View::composer('*', function ($view) {
            // Default nilai
            $notifications = [];
            $unreadCount = 0;

            // Cek apakah user login
            if (Auth::check()) {
                try {
                    $user = Auth::user();
                    $userId = $user->id;

                    // AMBIL 5 NOTIFIKASI TERBARU
                    $notifications = Notification::where('user_id', $userId)
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get()
                        ->map(function ($item) {
                            // Format data untuk frontend
                            return [
                                'id' => $item->id,
                                'title' => $item->title ?? 'Notifikasi',
                                'message' => $item->message ?? '',
                                'time' => $item->created_at->diffForHumans(),
                                'is_read' => $item->is_read === 'read',
                                'icon' => $item->icon ?? $this->getIcon($item->type),
                                'bg_color' => $item->bg_color ?? $this->getBgColor($item->type),
                                'text_color' => $item->text_color ?? $this->getTextColor($item->type),
                                'type' => $item->type,
                            ];
                        })->toArray();

                    // HITUNG YANG BELUM DIBACA
                    $unreadCount = Notification::where('user_id', $userId)
                        ->where('is_read', 'unread')
                        ->count();

                    // DEBUG KE LOG
                    \Log::info('AppServiceProvider: Notifikasi dishare', [
                        'user_id' => $userId,
                        'count' => count($notifications),
                        'unread' => $unreadCount
                    ]);

                } catch (\Exception $e) {
                    \Log::error('AppServiceProvider error: ' . $e->getMessage());
                }
            }

            // SHARE KE VIEW
            $view->with([
                'shared_notifications' => $notifications,
                'shared_unread_count' => $unreadCount,
            ]);
        });
    }

    /**
     * Helper untuk icon berdasarkan tipe
     */
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

    /**
     * Helper untuk background color
     */
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

    /**
     * Helper untuk text color
     */
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
}
