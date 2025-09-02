<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $table = 'units';
    protected $primarykey = 'id';
    protected $fillable = ['name','description'];
}
