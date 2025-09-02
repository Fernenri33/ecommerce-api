<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Status;

class Cart extends Model
{
    protected $table = 'carts';
    protected $primarykey = 'id';
    protected $fillable = ['user_id','status'];
    protected $cast = ['status' => CartStatus::class];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
