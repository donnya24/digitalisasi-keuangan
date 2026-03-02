<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notification extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'is_read',
        'data',
    ];

    protected $casts = [
        'is_read' => 'string', // <-- UBAH DARI boolean KE string
        'data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope untuk notifikasi yang belum dibaca
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', 'unread'); // <-- UBAH
    }

    /**
     * Scope untuk notifikasi yang sudah dibaca
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', 'read'); // <-- UBAH
    }

    /**
     * Scope untuk notifikasi yang diarsipkan
     */
    public function scopeArchived($query)
    {
        return $query->where('is_read', 'archived'); // <-- TAMBAHKAN
    }

    /**
     * Tandai sebagai sudah dibaca
     */
    public function markAsRead()
    {
        if ($this->is_read !== 'read') {
            $this->update(['is_read' => 'read']);
        }
        return $this;
    }

    /**
     * Tandai sebagai belum dibaca
     */
    public function markAsUnread()
    {
        if ($this->is_read !== 'unread') {
            $this->update(['is_read' => 'unread']);
        }
        return $this;
    }

    /**
     * Arsipkan notifikasi
     */
    public function archive()
    {
        if ($this->is_read !== 'archived') {
            $this->update(['is_read' => 'archived']);
        }
        return $this;
    }

    /**
     * Cek apakah sudah dibaca
     */
    public function isRead(): bool
    {
        return $this->is_read === 'read';
    }

    /**
     * Cek apakah belum dibaca
     */
    public function isUnread(): bool
    {
        return $this->is_read === 'unread';
    }

    /**
     * Cek apakah diarsipkan
     */
    public function isArchived(): bool
    {
        return $this->is_read === 'archived';
    }

    /**
     * Format waktu notifikasi
     */
    public function getFormattedTimeAttribute()
    {
        $now = Carbon::now();
        $diff = $now->diffInMinutes($this->created_at);
        
        if ($diff < 1) return 'baru saja';
        if ($diff < 60) return $diff . ' menit yang lalu';
        
        $diff = $now->diffInHours($this->created_at);
        if ($diff < 24) return $diff . ' jam yang lalu';
        
        $diff = $now->diffInDays($this->created_at);
        if ($diff < 7) return $diff . ' hari yang lalu';
        
        return $this->created_at->format('d M Y');
    }

    /**
     * Dapatkan icon berdasarkan tipe
     */
    public function getIconAttribute()
    {
        $icons = [
            'profit_decrease' => 'exclamation-triangle',
            'profit_increase' => 'chart-line',
            'large_expense' => 'bell',
            'prive' => 'money-bill-wave',
            'low_balance' => 'exclamation-circle',
            'target_achieved' => 'trophy',
            'transaction' => 'exchange-alt',
        ];

        return $icons[$this->type] ?? 'bell';
    }

    /**
     * Dapatkan warna background berdasarkan tipe
     */
    public function getBgColorAttribute()
    {
        $colors = [
            'profit_decrease' => 'bg-red-50',
            'profit_increase' => 'bg-green-50',
            'large_expense' => 'bg-yellow-50',
            'prive' => 'bg-blue-50',
            'low_balance' => 'bg-yellow-50',
            'target_achieved' => 'bg-green-50',
        ];

        return $colors[$this->type] ?? 'bg-gray-50';
    }

    /**
     * Dapatkan warna teks berdasarkan tipe
     */
    public function getTextColorAttribute()
    {
        $colors = [
            'profit_decrease' => 'text-red-600',
            'profit_increase' => 'text-green-600',
            'large_expense' => 'text-yellow-600',
            'prive' => 'text-blue-600',
            'low_balance' => 'text-yellow-600',
            'target_achieved' => 'text-green-600',
        ];

        return $colors[$this->type] ?? 'text-gray-600';
    }
}