<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\CartStatus;

class Cart extends Model
{
    use HasFactory;
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function cartItem(){
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
