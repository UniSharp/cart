<?php
namespace UniSharp\Cart\Contracts;

use UniSharp\Cart\Model\CartItem;

interface Validation
{
    public function validate(CartItem $item): bool;
}
