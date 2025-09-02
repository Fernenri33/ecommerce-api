<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
   protected $fillable = [
      'user_id',
      'line1',
      'line2',
      'city',
      'state',
      'postal_code',
      'country',
      'latitude',
      'longitude',
      'place_id',
   ];

   // Relación con el modelo User
   public function user()
   {
      return $this->belongsTo(User::class);
   }

   // Mutators para encriptar datos sensibles
   public function setLine1Attribute($value)
   {
      $this->attributes['line1'] = encrypt($value);
   }
   public function getLine1Attribute($value)
   {
      return decrypt($value);
   }

   public function setLine2Attribute($value)
   {
      $this->attributes['line2'] = $value ? encrypt($value) : null;
   }
   public function getLine2Attribute($value)
   {
      return $value ? decrypt($value) : null;
   }

   public function setCityAttribute($value)
   {
      $this->attributes['city'] = encrypt($value);
   }
   public function getCityAttribute($value)
   {
      return decrypt($value);
   }

   public function setStateAttribute($value)
   {
      $this->attributes['state'] = $value ? encrypt($value) : null;
   }
   public function getStateAttribute($value)
   {
      return $value ? decrypt($value) : null;
   }

   public function setPostalCodeAttribute($value)
   {
      $this->attributes['postal_code'] = $value ? encrypt($value) : null;
   }
   public function getPostalCodeAttribute($value)
   {
      return $value ? decrypt($value) : null;
   }

   public function setCountryAttribute($value)
   {
      $this->attributes['country'] = encrypt($value);
   }
   public function getCountryAttribute($value)
   {
      return decrypt($value);
   }
}
