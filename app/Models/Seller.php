<?php

namespace App\Models;

use App\Scopes\BuyerScope;

class Seller extends User
{
    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(new BuyerScope());
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
