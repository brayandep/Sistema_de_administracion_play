<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'reservation_id',
        'type',
        'minutes',
        'description',
    ];

    protected $casts = [
        'minutes' => 'integer',
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