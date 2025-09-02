<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
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
    protected $cast = [ 
        'status' => Status::class 
    ];
    public function unit(){
        $this->belongsTo(Unit::class);
    }
}
