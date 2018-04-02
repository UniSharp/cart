<?php
namespace UniSharp\Cart\Http\Controllers\Api\V1;

use UniSharp\Cart\CartManager;
use UniSharp\Cart\Models\Cart;
use UniSharp\Cart\OrderManager;
use Illuminate\Routing\Controller;
use UniSharp\Cart\Contracts\OrderContract;
use UniSharp\Cart\Http\Requests\StoreOrderRequest;
use UniSharp\Cart\Http\Requests\UpdateOrderRequest;
use UniSharp\Cart\Http\Requests\RefreshOrderRequest;

class OrdersController extends Controller
{
    public function index()
    {
        return app(OrderContract::class)
            ->with('items', 'receiverInformation', 'buyerInformation')
            ->paginate();
    }

    public function store(StoreOrderRequest $request)
    {
        return OrderManager::make()->checkout(
            CartManager::make(Cart::findOrFail($request->cart))->getItems(),
            [
                'buyer' => $request->buyer_information,
                'receiver' => $request->receiver_information
            ]
        )->getOrderInstance()->load('items', 'receiverInformation', 'buyerInformation');
    }

    public function update(OrderContract $order, UpdateOrderRequest $request)
    {
        $order->update($request->only('status', 'price', 'shipping_status'));

        $diff = collect($order->items->pluck('id'))->diff(collect($request->items)->pluck('id'));
        $order->items->whereIn('id', $diff->toArray())->each->delete();
        collect($request->items)->each(function ($item) use ($order) {
            $order->items->where('id', $item['id'])->first()->update($item);
        });

        if ($request->has('receiver_information')) {
            $result = $order->receiverInformation()->update($request->receiver_information);
        }

        if ($request->has('buyer_information')) {
            $order->buyerInformation()->update($request->buyer_information);
        }

        return ['success' => true];
    }

    public function destroy(OrderContract $order)
    {
        $order->delete();
        return [
            'success' => true
        ];
    }

    public function show(OrderContract $order)
    {
        return $order->load('items', 'receiverInformation', 'buyerInformation');
    }
}
