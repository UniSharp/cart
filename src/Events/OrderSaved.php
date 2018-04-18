<?php

namespace UniSharp\Cart\Events;

use Illuminate\Queue\SerializesModels;

class OrderSaved
{
    use SerializesModels;

    public $order;
    public $orderItems;

    public function __construct($order, $orderItems)
    {
        $this->order = $order;
        $this->orderItems = $orderItems;
    }
}
