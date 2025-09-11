<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSubcategory extends Model
{
    use HasFactory;
    public function subcategory(){
        $this->belongsTo(Subcategory::class);
    }
    public function product(){
        $this->belongsTo(Product::class);
    }
    protected $table = 'product_subcategories';
    protected $primarykey = 'id';
    protected $fillable = [
        'subcategory_id',
        'product_id'
    ];
}
