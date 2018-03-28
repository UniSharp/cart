<?php
namespace UniSharp\Cart\Contracts;

use UniSharp\Cart\Models\CartItem;

interface Validation
{
    public function validate(CartItem $item): bool;
}
