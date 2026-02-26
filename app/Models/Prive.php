<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Prive extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $table = 'prive';

    protected $fillable = [
        'user_id', 'amount', 'description', 'prive_date',
        'purpose', 'is_approved'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'prive_date' => 'date',
        'is_approved' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
