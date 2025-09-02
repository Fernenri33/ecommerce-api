<?php

enum ShipmentStatus:string{
    case Pending = 'pending';
    case Delivered = 'delivered';
    case OnTheWay = 'on_the_way';
    case Cancelled = 'cancelled';
}