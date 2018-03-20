<?php
namespace UniSharp\Cart\Model;

use UniSharp\Cart\Model\Item;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    public function items()
    {
        return $this->hasMany(Item::class, 'id');
    }
}
