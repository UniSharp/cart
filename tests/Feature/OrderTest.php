<?php
namespace UniSharp\Cart\Tests\Feature;

use UniSharp\Cart\CartManager;
use UniSharp\Cart\Models\Order;
use UniSharp\Cart\OrderManager;
use UniSharp\Cart\Enums\Payment;
use UniSharp\Cart\Tests\TestCase;
use UniSharp\Cart\Models\OrderItem;
use UniSharp\Cart\Enums\OrderStatus;
use UniSharp\Cart\Events\OrderSaved;
use Illuminate\Support\Facades\Event;
use UniSharp\Pricing\Facades\Pricing;
use UniSharp\Cart\Enums\OrderItemStatus;
use UniSharp\Cart\Tests\Fixtures\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function testMakeOrder()
    {
        $product = Product::create([
            'name' => 'Product A',
            'price' => 20,
            'sku' => 'B-1',
            'stock' => 20,
            'sold_qty' => $origin_qty = 2
        ]);

        $spec = $product->specs->first();
        $cart = CartManager::make()->add($spec, $qty = 1)->save();
        OrderManager::setSerialNumberResolver(function () {
            return 'ABC-1';
        });
        Pricing::shouldReceive('setItems')->andReturnSelf();
        Pricing::shouldReceive('apply')->andReturnSelf();
        Pricing::shouldReceive('getTotal')->andReturn(100);
        Event::fake();

        $manager = OrderManager::make()->checkout($cart->getItems(), [
            'payment' => 'credit',
            'receiver' => [
                'name' => 'User A',
                'address' => 'A 區 B 縣',
                'phone' => '0912345678',
                'email' => 'fk@example.com'
            ],
            'buyer' => [
                'name' => 'User A',
                'address' => 'A 區 B 縣',
                'phone' => '0912345678',
                'email' => 'fk@example.com'
            ]
        ]);

        $this->assertDatabaseHas('orders', $order = [
            'id' => $manager->getOrderInstance()->id,
            'status' => OrderStatus::PENDDING,
            'total_price' => 100,
            'sn' => 'ABC-1'
        ]);

        $this->assertDatabaseHas('order_items', $order_item = [
            'status' => OrderItemStatus::NORMAL,
            'price' => 20,
            'spec' => 'default',
            'sku' => 'B-1',
            'quantity' => $qty,
        ]);

        $this->assertDatabaseHas('information', [
            'type' => 'receiver',
            'name' => 'User A',
            'address' => 'A 區 B 縣',
            'phone' => '0912345678',
            'email' => 'fk@example.com'
        ]);

        $this->assertDatabaseHas('information', [
            'type' => 'buyer',
            'name' => 'User A',
            'address' => 'A 區 B 縣',
            'phone' => '0912345678',
            'email' => 'fk@example.com'
        ]);

        $this->assertDatabaseHas('specs', [
            'price' => 20,
            'sku' => 'B-1',
            'stock' => 20,
            'sold_qty' => $origin_qty + $qty
        ]);

        Event::assertDispatched(OrderSaved::class, function ($e) use ($order, $order_item) {
            $this->assertArraySubset($order, $e->order->toArray());
            $this->assertArraySubset($order_item, $e->orderItems->toArray()[0]);
            return true;
        });
    }

    public function testDelete()
    {
        $order = Order::create([
            'payment' => Payment::CREDIT,
            'status' => OrderStatus::PENDDING,
            'total_price' => 100,
            'sn' => 'ABC-1'
        ]);

        $order->items()->save($orderItem = OrderItem::create([
            'status' => OrderItemStatus::NORMAL,
            'price' => 20,
            'spec' => 'default',
            'sku' => 'B-1',
            'quantity' => 10,
        ]));

        $order->delete();

        $this->assertSoftDeleted('orders', [
            'id' => $order->id,
            'payment' => Payment::CREDIT,
            'status' => OrderStatus::PENDDING,
            'total_price' => 100,
            'sn' => 'ABC-1'
        ]);

        $this->assertSoftDeleted('order_items', [
            'id' => $orderItem->id,
            'status' => OrderItemStatus::NORMAL,
            'price' => 20,
            'spec' => 'default',
            'sku' => 'B-1',
            'quantity' => 10,
        ]);
    }
}
