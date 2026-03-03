<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Prive extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $table = 'prive';

    protected $fillable = [
        'user_id',
        'purpose_id',        // <-- TAMBAHKAN
        'amount',
        'description',
        'prive_date',
        'purpose',
        'is_approved',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'prive_date' => 'date',
        'is_approved' => 'string',
    ];

    /**
     * Relasi ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke keperluan prive
     */
    public function purposeModel()
    {
        return $this->belongsTo(PrivePurpose::class, 'purpose_id');
    }

    /**
     * Scope untuk yang sudah disetujui
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', 'approved');
    }

    /**
     * Scope untuk yang pending
     */
    public function scopePending($query)
    {
        return $query->where('is_approved', 'pending');
    }

    /**
     * Format waktu
     */
    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->prive_date)->translatedFormat('d F Y');
    }

    /**
     * Format amount
     */
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }
}