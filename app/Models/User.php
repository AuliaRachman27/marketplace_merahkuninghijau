<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    protected $fillable = ['name','email','password','role'];
    protected $hidden = ['password','remember_token'];

    public function getJWTIdentifier() {
        return $this->getKey();
    }
    public function getJWTCustomClaims() {
        return [];
    }

    public function products() {
        return $this->hasMany(Product::class, 'merchant_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'customer_id');
    }
}
