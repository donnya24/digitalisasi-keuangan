<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PrivePurpose extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $table = 'prive_purposes';

    protected $fillable = [
        'user_id',
        'name',
        'description',
        // 'icon',        // <-- HAPUS
        // 'color',       // <-- HAPUS (opsional)
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'string',
        'sort_order' => 'integer',
    ];

    /**
     * Relasi ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke prive
     */
    public function prives()
    {
        return $this->hasMany(Prive::class, 'purpose_id');
    }

    /**
     * Scope aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 'active');
    }

    /**
     * Scope urut berdasarkan sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Format untuk dropdown
     */
    public function getDisplayNameAttribute()
    {
        return ucfirst($this->name);
    }
}