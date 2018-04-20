<?php
namespace UniSharp\Cart\Enums;

use Konekt\Enum\Enum;
use UniSharp\Cart\Contracts\PaymentContract;

class Payment extends Enum implements PaymentContract
{
    const CREDIT = 'credit';
    const ATM = 'atm';
    const CVS = 'cvs';
    const BARCODE = 'barcode';
    const __default = self::CREDIT; 
}
