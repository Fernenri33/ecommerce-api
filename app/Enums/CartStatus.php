<?php

enum CartStatus:string{
    case Active = 'active';
    case Cancelled = 'cancelled';
    case CheckedOut = 'checked_out';
}