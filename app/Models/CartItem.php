<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    public function cart(){
        return $this->belongsTo(Cart::class);
    }
    public function price(){
        return $this->belongsTo(Price::class);
    }
    
    protected $table = 'cart_items';
    protected $primarykey = 'id';
    protected $fillable = ['cart_id','price_id'];
}
