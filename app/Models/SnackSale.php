<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SnackSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_number',
        'user_id',
        'payment_method',
        'subtotal',
        'total',
        'status',
        'sold_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'sold_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(SnackSaleItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}