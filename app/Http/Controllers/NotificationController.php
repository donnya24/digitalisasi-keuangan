<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Menampilkan semua notifikasi
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Notification::where('user_id', $user->id);
        
        // Filter berdasarkan status
        if ($request->has('filter')) {
            if ($request->filter === 'unread') {
                $query->where('is_read', 'unread'); // <-- UBAH
            } elseif ($request->filter === 'read') {
                $query->where('is_read', 'read'); // <-- UBAH
            } elseif ($request->filter === 'archived') {
                $query->where('is_read', 'archived'); // <-- TAMBAHKAN
            }
        }
        
        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', 'unread') // <-- UBAH
            ->count();
        
        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Menandai notifikasi sebagai sudah dibaca
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);
        
        $notification->markAsRead();
        
        return redirect()->back()->with('success', 'Notifikasi ditandai sudah dibaca');
    }

    /**
     * Menandai semua notifikasi sebagai sudah dibaca
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', 'unread') // <-- UBAH
            ->update(['is_read' => 'read']);
        
        return redirect()->back()->with('success', 'Semua notifikasi ditandai sudah dibaca');
    }

    /**
     * Menghapus notifikasi
     */
    public function destroy($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);
        
        $notification->delete();
        
        return redirect()->back()->with('success', 'Notifikasi berhasil dihapus');
    }

    /**
     * Menghapus semua notifikasi
     */
    public function destroyAll()
    {
        Notification::where('user_id', Auth::id())->delete();
        
        return redirect()->back()->with('success', 'Semua notifikasi berhasil dihapus');
    }

    /**
     * Menghapus notifikasi yang sudah dibaca
     */
    public function destroyRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', 'read') // <-- UBAH
            ->delete();
        
        return redirect()->back()->with('success', 'Notifikasi yang sudah dibaca berhasil dihapus');
    }

    /**
     * Menghapus notifikasi yang diarsipkan
     */
    public function destroyArchived()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', 'archived') // <-- TAMBAHKAN
            ->delete();
        
        return redirect()->back()->with('success', 'Notifikasi yang diarsipkan berhasil dihapus');
    }

    /**
     * Arsipkan notifikasi
     */
    public function archive($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);
        
        $notification->archive();
        
        return redirect()->back()->with('success', 'Notifikasi diarsipkan');
    }

    /**
     * Mendapatkan semua notifikasi untuk modal (dengan limit lebih besar)
     */
    public function latest(Request $request)
    {
        $user = Auth::user();
        $limit = $request->get('limit', 50);
        
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'title' => $notif->title,
                    'message' => $notif->message,
                    'time' => $notif->formatted_time,
                    'icon' => $notif->icon,
                    'bg_color' => $notif->bg_color,
                    'text_color' => $notif->text_color,
                    'is_read' => $notif->is_read === 'read',
                    'type' => $notif->type,
                ];
            });
        
        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', 'unread')
            ->count();
        
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }
}