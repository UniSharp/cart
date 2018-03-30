<?php
namespace UniSharp\Cart\Http\Controllers\Api\V1;

use UniSharp\Cart\CartManager;
use UniSharp\Cart\Models\Cart;
use UniSharp\Cart\OrderManager;
use Illuminate\Routing\Controller;
use UniSharp\Cart\Models\Order as OrderModel;
use UniSharp\Cart\Http\Requests\StoreOrderRequest;
use UniSharp\Cart\Http\Requests\UpdateOrderRequest;
use UniSharp\Cart\Http\Requests\RefreshOrderRequest;

class OrdersController extends Controller
{
    public function store(StoreOrderRequest $request)
    {
        return OrderManager::make()->checkout(
            CartManager::make(Cart::findOrFail($request->cart))->getItems(),
            $request->informations
        )->getOrderInstance()->load('items', 'receiverInformation', 'buyerInformation');
    }

    public function update()
    {
    }

    public function delete()
    {
    }

    public function destroy()
    {
    }

    public function show()
    {
    }
}
