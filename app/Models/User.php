<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
    public function address()
    {
        return $this->hasMany(UserAddress::class);
    }
    public function rol()
    {
        return $this->hasOne(Role::class);
    }
    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class, 'user_favorite_products');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'email_verified_at',
        'password',
        'rol_id',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Mutators para encriptar datos sensibles
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = encrypt($value);
    }

    public function getNameAttribute($value)
    {
        return decrypt($value);
    }

    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = encrypt($value);
    }

    public function getLastNameAttribute($value)
    {
        return decrypt($value);
    }

    public function setEmailAttribute($value)
    {
        $normalized = strtolower(trim($value));

        $this->attributes['email'] = encrypt($value);
        $this->attributes['email_hash'] = hash_hmac('sha256', $normalized, config('app.key'));
    }

    public function getEmailAttribute($value)
    {
        return decrypt($value);
    }
}
