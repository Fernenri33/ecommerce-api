<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Status;

class Discount extends Model
{
    use HasFactory;
    protected $table = 'discounts';
    protected $primarykey = 'id';
    protected $fillable = [
        'name',
        'description',
        'cuantity',
        'status'
    ];
    protected $casts = [ 
        'Status' => Status::class 
    ];

}
