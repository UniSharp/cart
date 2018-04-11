<?php
namespace UniSharp\Cart\Models;

use UniSharp\Cart\Models\CartItem;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
