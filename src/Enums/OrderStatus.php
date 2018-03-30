<?php
namespace UniSharp\Cart\Enums;

use Konekt\Enum\Enum;
use UniSharp\Cart\Contracts\OrderStatusContract;

class OrderStatus extends Enum implements OrderStatusContract
{
    const __default = self::PENDDING;
    const PENDDING = 0;
    const COMPLETED = 1;
    const CANCELED = 2;
}
