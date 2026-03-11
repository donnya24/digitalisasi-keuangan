<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'phone',
        'avatar',
        'email_verified_at',
        'last_login',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login' => 'datetime',
        ];
    }

    // ========== RELASI ==========
    /**
     * Relasi ke bisnis (satu user memiliki satu bisnis)
     */
    public function business()
    {
        return $this->hasOne(Business::class, 'user_id', 'id');
    }

    // ========== ACCESSORS ==========
    /**
     * Accessor untuk mendapatkan URL Avatar lengkap dari Supabase.
     * Jika avatar tidak ada, gunakan UI Avatars sebagai fallback.
     */
    public function getAvatarUrlAttribute()
    {
        if (!$this->avatar) {
            // Return default avatar
            return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=0D8ABC&color=fff&size=128';
        }

        $projectRef = env('SUPABASE_PROJECT_REF');
        if (!$projectRef) {
            // Fallback jika env tidak ada (tidak mungkin, tapi antisipasi)
            return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=0D8ABC&color=fff&size=128';
        }
        // Pastikan bucket 'avatars' adalah public
        return "https://{$projectRef}.supabase.co/storage/v1/object/public/avatars/{$this->avatar}";
    }

    // ========== SCOPES ==========
    public function scopeActive($query)
    {
        return $query->where('is_active', 'active');
    }

    // ========== HELPER METHODS ==========
    public function markAsActive(): void
    {
        $this->update(['is_active' => 'active']);
    }

    public function markAsInactive(): void
    {
        $this->update(['is_active' => 'inactive']);
    }

    public function markAsSuspended(): void
    {
        $this->update(['is_active' => 'suspended']);
    }

    public function isActive(): bool
    {
        return $this->is_active === 'active';
    }

    public function isInactive(): bool
    {
        return $this->is_active === 'inactive';
    }

    public function isSuspended(): bool
    {
        return $this->is_active === 'suspended';
    }
}