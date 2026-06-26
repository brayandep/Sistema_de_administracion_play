<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
    'customer_id',
    'station_id',
    'user_id',
    'people_count',
    'started_at',
    'ended_at',
    'duration_minutes',
    'paid_minutes',
    'free_minutes',
    'hourly_rate',
    'subtotal',
    'discount',
    'total',
    'payment_method',
    'status',
    'reward_ticket_id',
];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'people_count' => 'integer',
        'hourly_rate' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function rewardTicket()
    {
        return $this->belongsTo(RewardTicket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}