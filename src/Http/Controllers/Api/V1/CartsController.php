<?php
namespace UniSharp\Cart\Http\Controllers\Api\V1;

use UniSharp\Cart\Cart;
use Illuminate\Routing\Controller;
use UniSharp\Cart\Model\Cart as CartModel;
use UniSharp\Cart\Http\Requests\StoreCartRequest;
use UniSharp\Cart\Http\Requests\UpdateCartRequest;

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

    public function update(UpdateCartRequest $request, CartModel $cart)
    {
        $cart = Cart::create($cart);
        collect($request->specs)->each(function ($spec) use ($cart) {
            $cart->add($spec['id'], $spec['quentity']);
        });

        $cart->save();
        return $cart->getCartInstance()->load('items');
    }

    public function delete(CartModel $cart, $item)
    {
        Cart::create($cart)->remove($item)->save();
        return [
            'success' => true
        ];
    }

    public function destroy(CartModel $cart)
    {
        $cart->delete();
        return [
            'success' => true
        ];
    }

    public function show(CartMdoel $cart)
    {
        return $cart->load('items');
    }
}
