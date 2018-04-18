<?php
namespace UniSharp\Cart\Models;

use UniSharp\Buyable\Models\Spec;
use Illuminate\Database\Eloquent\Model;
use UniSharp\Cart\Contracts\OrderItemContract;

class OrderItem extends Model implements OrderItemContract
{
    protected $fillable = ['id', 'name', 'spec', 'sku', 'price', 'order_id', 'quantity', 'spec_id'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function spec()
    {
        return $this->belongsTo(Spec::class);
    }
}
