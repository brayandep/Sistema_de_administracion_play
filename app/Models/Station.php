<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'hourly_rate',
        'status',
        'active',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function activeReservation()
    {
        return $this->hasOne(Reservation::class)
            ->where('status', 'activa')
            ->latest('id');
    }

    public function isAvailable()
    {
        return $this->active &&
            $this->status === 'disponible';
    }
}