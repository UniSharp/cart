<?php
namespace UniSharp\Cart\Http\Controllers\Api\V1;

use UniSharp\Cart\CartManager;
use UniSharp\Cart\Models\Cart;
use UniSharp\Cart\OrderManager;
use Illuminate\Routing\Controller;
use UniSharp\Cart\Enums\PaymentStatus;
use UniSharp\Cart\Contracts\OrderContract;
use UniSharp\Cart\Contracts\OrderItemContract;
use UniSharp\Cart\Http\Requests\StoreOrderRequest;
use UniSharp\Cart\Http\Requests\UpdateOrderRequest;
use UniSharp\Cart\Contracts\OrderItemStatusContract;
use UniSharp\Cart\Http\Requests\RefreshOrderRequest;
use VoiceTube\TaiwanPaymentGateway\Common\GatewayInterface;
use VoiceTube\TaiwanPaymentGateway\Common\ResponseInterface;

class OrdersController extends Controller
{
    public function index()
    {
        return app(OrderContract::class)
            ->latest()
            ->with('items', 'receiverInformation', 'buyerInformation')
            ->paginate();
    }

    public function store(StoreOrderRequest $request)
    {
        $manager = OrderManager::make();
        if (auth()->user()) {
            $manager->assign(auth()->user());
        }

        $response = $manager->checkout(
            CartManager::make(Cart::findOrFail($request->cart))->getItems(),
            [
                'buyer' => $request->buyer_information,
                'receiver' => $request->receiver_information,
                'payment' => $request->payment
            ]
        )->getOrderInstance()->load('items', 'receiverInformation', 'buyerInformation');

        Cart::destroy($request->cart);

        return $response;
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

    public function delete(OrderContract $order, $item)
    {
        $item = app(OrderItemContract::class)->find($item);
        $item->status = app(OrderItemStatusContract::class)::CANCELED;
        $item->save();

        $order->total_price = $order->total_price - ($item->quality ?? 1) * $item->price;
        $order->save();

        return ['success' => true];
    }

    public function pay(GatewayInterface $gateway, OrderContract $order)
    {
        $gateway->newOrder(
            $order->sn,
            $order->total_price,
            $order->name,
            $order->note
        );

        $payment = studly_case($order->payment->value());
        $gateway->{"use{$payment}"}();
        return $gateway->genForm(true);
    }
}
