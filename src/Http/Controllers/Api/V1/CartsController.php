<?php
namespace UniSharp\Cart\Http\Controllers\Api\V1;

use UniSharp\Cart\CartManager;
use Illuminate\Routing\Controller;
use UniSharp\Cart\Models\Cart as CartModel;
use UniSharp\Cart\Http\Requests\StoreCartRequest;
use UniSharp\Cart\Http\Requests\UpdateCartRequest;
use UniSharp\Cart\Http\Requests\RefreshCartRequest;

class CartsController extends Controller
{
    public function store(StoreCartRequest $request)
    {
        $cart = CartManager::make();
        collect($request->specs)->each(function ($spec) use ($cart) {
            $cart->add($spec['id'], $spec['quentity']);
        });

        $cart->save();
        return $cart->getCartInstance()->load('items');
    }

    public function update(UpdateCartRequest $request, CartModel $cart)
    {
        $cart = CartManager::make($cart);
        collect($request->specs)->each(function ($spec) use ($cart) {
            $cart->add($spec['id'], $spec['quentity']);
        });

        if (auth()->user()) {
            $cart->assign(auth()->user());
        }

        $cart->save();
        return $cart->getCartInstance()->load('items');
    }

    public function refresh(RefreshCartRequest $request, CartModel $cart)
    {
        $cart = CartManager::make($cart)->clean();

        collect($request->specs)->each(function ($spec) use ($cart) {
            $cart->add($spec['id'], $spec['quentity']);
        });

        if (auth()->user()) {
            $cart->assign(auth()->user());
        }

        $cart->save();
        return $cart->getCartInstance()->load('items');
    }

    public function delete(CartModel $cart, $item)
    {
        CartManager::make($cart)->remove($item)->save();
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

    public function show(CartModel $cart)
    {
        return $cart->load('items');
    }
}
