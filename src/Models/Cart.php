<?php
namespace UniSharp\Cart\Models;

use UniSharp\Cart\CartManager;
use UniSharp\Cart\Models\CartItem;
use Illuminate\Database\Eloquent\Model;
use UniSharp\Cart\Contracts\CartContract;
use UniSharp\Cart\Contracts\CartItemContract;

class Cart extends Model implements CartContract
{
    protected $appends = [
        'price', 'fee', 'originalPrice'
    ];

    public function items()
    {
        return $this->hasMany(get_class(resolve(CartItemContract::class)));
    }

    public function getPriceAttribute()
    {
        return CartManager::make($this)->getPrice();
    }

    public function getOriginalPriceAttribute()
    {
        return CartManager::make($this)->getOriginalPrice();
    }

    public function getFeeAttribute()
    {
        return CartManager::make($this)->getFee();
    }
}
