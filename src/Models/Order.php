<?php
namespace UniSharp\Cart\Models;

use UniSharp\Cart\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'id');
    }
}
