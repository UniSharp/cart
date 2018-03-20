<?php
namespace UniSharp\Cart\Model;

use UniSharp\Cart\Model\CartItem;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    public function items()
    {
        return $this->hasMany(CartItem::class, 'id');
    }
}
