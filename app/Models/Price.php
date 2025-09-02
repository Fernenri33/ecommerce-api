<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Status;

class Price extends Model
{
    public function products(){
        return $this->belongsTo(Product::class);
    }
    public function discounts(){
        return $this->belongsTo(Discount::class);
    }
    public function cartItems(){
        return $this->hasMany(CartItem::class);
    }
    public function carts(){
        return $this->belongsToMany(Cart::class, 'cart_items');
    }
    protected $table = 'prices';
    protected $primarykey = 'id';
    protected $fillable = [
        'name',
        'product_id',
        'description',
        'cuantity',
        'price',
        'discount',
        'status'
    ];
    protected $casts =[
        'status'=>Status::class
    ];
}
