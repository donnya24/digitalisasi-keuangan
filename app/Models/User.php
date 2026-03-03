<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'phone',
        'avatar',
        'email_verified_at',
        'last_login',
        'is_active'  // Sekarang menerima 'active', 'inactive', 'suspended'
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
            // 'is_active' => 'boolean', // HAPUS atau komen baris ini
        ];
    }

    // Accessor untuk mengecek status aktif
    public function getIsActiveAttribute($value)
    {
        return $value;
    }

    // Scope untuk user aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', 'active');
    }

    // Helper methods
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
