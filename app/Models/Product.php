<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Status;

class Product extends Model
{
    use HasFactory;
    public function unit(){
        return $this->belongsTo(Unit::class);
    }
    public function productSubCategories(){
        return $this->hasMany(ProductSubcategory::class);
    }
    public function subcategories(){
        return $this->belongsToMany(Subcategory::class, 'product_subcategories');
    }
    public function UserFavorites(){
        return $this->belongsToMany(User::class, 'user_favorite_products');
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
        'status'
    ];
    protected $casts = [ 
        'Status' => Status::class 
    ];
}
