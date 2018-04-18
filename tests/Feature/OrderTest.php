<?php
namespace UniSharp\Cart\Tests\Feature;

use UniSharp\Cart\CartManager;
use UniSharp\Cart\OrderManager;
use UniSharp\Cart\Tests\TestCase;
use UniSharp\Cart\Enums\OrderStatus;
use UniSharp\Pricing\Facades\Pricing;
use UniSharp\Cart\Tests\Fixtures\Product;
use UniSharp\Cart\Events\OrderSaved;
use Illuminate\Support\Facades\Event;
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
        ]);

        $spec = $product->specs->first();
        $cart = CartManager::make()->add($spec, 1)->save();
        OrderManager::setSerialNumberResolver(function () {
            return 'ABC-1';
        });
        Pricing::shouldReceive('setItems')->andReturnSelf();
        Pricing::shouldReceive('apply')->andReturnSelf();
        Pricing::shouldReceive('getTotal')->andReturn(100);
        Event::fake();

        $manager = OrderManager::make()->checkout($cart->getItems(), [
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

        $this->assertDatabaseHas('order_items', $orderItem = [
            'price' => 20,
            'spec' => 'default',
            'sku' => 'B-1',
            'quantity' => 1,
            'buyable_type' => $spec->buyable_type,
            'buyable_id' => $spec->buyable_id,
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

        Event::assertDispatched(OrderSaved::class, function ($e) use ($order, $orderItem) {
            $this->assertArraySubset($order, $e->order->toArray());
            $this->assertArraySubset($orderItem, $e->orderItems->toArray()[0]);
            return true;
        });
    }
}
