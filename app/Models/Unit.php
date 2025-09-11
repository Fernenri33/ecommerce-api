<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;
    public function product(){
        return $this->hasMany(Product::class);
    }
    protected $table = 'units';
    protected $primarykey = 'id';
    protected $fillable = ['name','description'];
}
