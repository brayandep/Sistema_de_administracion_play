<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SnackSaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'snack_sale_id',
        'product_id',
        'product_name',
        'product_type',
        'quantity',
        'unit_price',
        'total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(SnackSale::class, 'snack_sale_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}