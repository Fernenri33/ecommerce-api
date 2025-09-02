<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use CartStatus;

class Cart extends Model
{
    public function users(){
        return $this->belongsTo(User::class);
    }
    public function cartItems(){
        return $this->hasMany(CartItem::class);
    }
    public function prices(){
        return $this->belongsToMany(Price::class, 'cart_items');
    }
    protected $table = 'carts';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id','status'];
    protected $casts = ['status' => CartStatus::class];
}
