<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'product_id',
        'price_id',
        'quantity',
        'unit_price',
        'currency',
        // 'discount_id'
    ];

    protected $casts = [
        'quantity'   => 'int',
        'unit_price' => 'int',
    ];

    public function order()   { return $this->belongsTo(Order::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function price()   { return $this->belongsTo(Price::class); }
    // public function discount(){ return $this->belongsTo(Discount::class); }
}
