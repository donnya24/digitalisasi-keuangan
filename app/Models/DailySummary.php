<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DailySummary extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $table = 'daily_summaries';

    protected $fillable = [
        'user_id', 'date', 'total_income', 'total_expense',
        'net_profit', 'cash_balance'
    ];

    protected $casts = [
        'date' => 'date',
        'total_income' => 'decimal:2',
        'total_expense' => 'decimal:2',
        'net_profit' => 'decimal:2',
        'cash_balance' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
