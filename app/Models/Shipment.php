<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $table = 'shipments';
    protected $primarykey = 'id';
    protected $fillable = [
        'user_address_id',
        'oreder_id',
        'status',
        'courier',
        'delivery_date'
    ];
    protected $cast = [
        'status'=>ShipmentStatus::class
    ];

    public function userAddress()
    {
        return $this->belongsTo(UserAddress::class, 'user_address_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
