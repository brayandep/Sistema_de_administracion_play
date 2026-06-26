<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'quantity',
        'description',
        'price',
        'product_type',
        'available',
        'image',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'available' => 'boolean',
    ];
}