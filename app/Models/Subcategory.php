<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;
    public function categories(){
        return $this->belongsTo(Category::class);
    }
    public function productSubcategory(){
        return $this->hasMany(ProductSubcategory::class);
    }
    public function products(){
        return $this->belongsToMany(Product::class, 'product_subcategories');
    }
    protected $table = 'subcategories';
    protected $primarykey = 'id';
    protected $fillable = ['category_id','name','description'];
}
