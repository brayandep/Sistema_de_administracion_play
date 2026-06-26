<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'phone',
        'notes',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
    

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function loyaltyMovements()
    {
        return $this->hasMany(LoyaltyMovement::class);
    }

    public function rewardTickets()
    {
        return $this->hasMany(RewardTicket::class);
    }

    public function availableRewardTickets()
    {
        return $this->rewardTickets()
            ->where('status', 'disponible');
    }

    public function availableLoyaltyMinutes()
    {
        return (int) $this->loyaltyMovements()
            ->sum('minutes');
    }

    public function availableLoyaltyHours()
    {
        return round($this->availableLoyaltyMinutes() / 60, 2);
    }
}