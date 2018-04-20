<?php
namespace UniSharp\Cart\Enums;

use Konekt\Enum\Enum;
use UniSharp\Cart\Contracts\PaymentStatusContract;

class PaymentStatus extends Enum implements PaymentStatusContract
{
    const __default = self::PENDDING;
    const PENDDING = 0;
    const COMPLETE = 1;
    const CANCEL = 2;
    const FAILED = 3;
}
