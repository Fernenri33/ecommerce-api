<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Status;

class Price extends Model
{
    use HasFactory;
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
    public function carts()
    {
        return $this->belongsToMany(Cart::class, 'cart_items');
    }
    public function scopeSellable($q)
    {
        return $q->where('status', 'active')
            ->where(function ($qq) {
                $qq->whereNull('discount_id')
                    ->orWhereHas('discount', fn($d) => $d->where('status', 'active'));
            });
    }
    protected $table = 'prices';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'product_id',
        'description',
        'quantity',
        'price',
        'discount_id',
        'status'
    ];
    protected $casts = [
        'Status' => Status::class
    ];
}
