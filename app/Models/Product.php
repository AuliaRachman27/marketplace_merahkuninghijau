<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['merchant_id','name','price','stock','description'];
    public function merchant() {
        return $this->belongsTo(User::class,'merchant_id');
    }
}
