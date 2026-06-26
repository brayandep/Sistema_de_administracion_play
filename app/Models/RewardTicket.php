<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'code',
        'free_minutes',
        'status',
        'generated_at',
        'used_at',
        'reservation_id',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'used_at' => 'datetime',
        'free_minutes' => 'integer',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}