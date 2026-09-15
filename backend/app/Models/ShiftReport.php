<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_id',
        'total_orders',
        'total_beverages_sold',
        'total_sessions',
        'total_revenue',
        'cash_transactions',
        'card_transactions',
    ];

    protected $casts = [
        'total_orders' => 'integer',
        'total_beverages_sold' => 'integer',
        'total_sessions' => 'integer',
        'total_revenue' => 'decimal:2',
        'cash_transactions' => 'decimal:2',
        'card_transactions' => 'decimal:2',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }
}
