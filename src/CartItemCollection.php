<?php
namespace UniSharp\Cart;

use Illuminate\Support\Collection;

class CartItemCollection extends Collection
{
    public function __construct($items = [])
    {
        $items = array_filter($items, function ($item) {
            return $this->validate($item);
        });

        parent::__construct($items);
    }

    public function validate($item)
    {
        $validationClass = config('cart.validations', []);
        foreach ($validationClass as $class) {
            if (!app($class)->validate($item)) {
                return false;
            }
        }

        return true;
    }

    public function offsetSet($key, $value)
    {
        if ($this->validate($value)) {
            parent::offsetSet($key, $value);
        }
    }
}
