<?php
namespace UniSharp\Cart\Http\Controllers\Api\V1;

use UniSharp\Cart\Cart;
use Illuminate\Routing\Controller;
use UniSharp\Cart\Model\CartModel;
use UniSharp\Cart\Http\Requests\StoreCartRequest;

class CartsController extends Controller
{
    public function store(StoreCartRequest $request)
    {
        $cart = Cart::create();
        collect($request->specs)->each(function ($spec) use ($cart) {
            $cart->add($spec['id'], $spec['quentity']);
        });

        $cart->save();
        return $cart->getCartInstance()->load('items');
    }

    public function show(CartMdoel $cart)
    {
        return $cart->load('items');
    }
}
