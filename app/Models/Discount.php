<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Status;

class Discount extends Model
{
    protected $table = 'discounts';
    protected $primarykey = 'id';
    protected $fillable = [
        'name',
        'description',
        'cuantity',
        'status'
    ];
    protected $cast = ['status'=>Status::class];

}
