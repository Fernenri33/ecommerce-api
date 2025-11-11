<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\Status;

class Product extends Model
{
    use HasFactory;
    
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    
    // ELIMINADO: productSubCategories() - no lo necesitas
    
    public function subcategories()
    {
        return $this->belongsToMany(Subcategory::class, 'product_subcategories', 'product_id', 'subcategory_id');
    }
    
    public function UserFavorites()
    {
        return $this->belongsToMany(User::class, 'user_favorite_products');
    }
    
    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    public function scopeSellable($q)
    {
        return $q->where('status', 'active');
    }

    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'sku',
        'description',
        'imagen',
        'available_quantity',
        'warehouse_quantity',
        'unit_id',
        'status',
        'unit_cost'
    ];
    
    protected $casts = [ 
        'status' => Status::class  // Cambiado 'Status' a 'status' (minúscula)
    ];
}