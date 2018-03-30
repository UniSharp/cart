<?php
namespace UniSharp\Cart\Enums;

use Konekt\Enum\Enum;
use UniSharp\Cart\Contracts\ShippingStatusContract;

class ShippingStatus extends Enum implements ShippingStatusContract
{
    const __default = self::PENDDING;
    const PENDDING = 0;
    const COMPLETE = 1;
    const CANCEL = 2;
}
