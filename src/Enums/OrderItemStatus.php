<?php
namespace UniSharp\Cart\Enums;

use Konekt\Enum\Enum;
use UniSharp\Cart\Contracts\OrderItemStatusContract;

class OrderItemStatus extends Enum implements OrderItemStatusContract
{
    const __default = self::NORMAL;
    const NORMAL = 0;
    const CANCELED = 1;
}
