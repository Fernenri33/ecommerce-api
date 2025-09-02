<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Status;

class Price extends Model
{
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
    protected $cast =[
        'status'=>Status::class
    ];
    public function product(){
        return $this->belongsTo(Product::class);
    }
}
