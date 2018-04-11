<?php
namespace UniSharp\Cart\Models;

use UniSharp\Cart\Models\CartItem;
use Illuminate\Database\Eloquent\Model;
use UniSharp\Cart\Contracts\CartItemContract;
use UniSharp\Cart\Contracts\CartContract;

class Cart extends Model implements CartContract
{
    public function items()
    {
        return $this->hasMany(get_class(resolve(CartItemContract::class)));
    }
}
