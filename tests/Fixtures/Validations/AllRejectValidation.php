<?php
namespace UniSharp\Cart\Tests\Fixtures\Validations;

use UniSharp\Cart\Models\CartItem;
use UniSharp\Cart\Contracts\Validation;

class AllRejectValidation implements Validation
{
    public function validate(CartItem $item): bool
    {
        return false;
    }
}
