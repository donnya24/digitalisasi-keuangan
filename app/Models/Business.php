<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id', 'business_name', 'business_type', 'phone',
        'address', 'city', 'province', 'postal_code',
        'logo', 'opening_hours'
    ];

    protected $casts = [
        'opening_hours' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
