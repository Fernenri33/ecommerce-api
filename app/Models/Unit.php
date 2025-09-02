<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    public function product(){
        return $this->hasMany(Product::class);
    }
    protected $table = 'units';
    protected $primarykey = 'id';
    protected $fillable = ['name','description'];
}
