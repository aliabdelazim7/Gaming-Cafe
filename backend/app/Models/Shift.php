<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'start_time',
        'end_time',
        'status',
        'total_before_deductions',
        'total_after_deductions',
        'deductions',
        'cash_collected',
        'card_collected',
        'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'total_before_deductions' => 'decimal:2',
        'total_after_deductions' => 'decimal:2',
        'deductions' => 'decimal:2',
        'cash_collected' => 'decimal:2',
        'card_collected' => 'decimal:2',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function sessions()
    {
        return $this->hasMany(DeviceSession::class, 'shift_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'shift_id');
    }

    public function report()
    {
        return $this->hasOne(ShiftReport::class, 'shift_id');
    }
}
