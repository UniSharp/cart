<?php
namespace UniSharp\Cart\Models;

use UniSharp\Buyable\Models\Spec;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['id', 'name', 'spec', 'sku', 'price', 'order_id', 'quentity'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
