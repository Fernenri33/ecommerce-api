<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function unit(){
        return $this->belongsTo(Unit::class);
    }
    public function productSubCategories(){
        return $this->hasMany(ProductSubcategory::class);
    }
    public function subcategories(){
        return $this->belongsToMany(Subcategory::class, 'product_subcategories');
    }
    protected $table = 'products';
    protected $primarykey = 'id';
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
        'status' => Status::class 
    ];
}
