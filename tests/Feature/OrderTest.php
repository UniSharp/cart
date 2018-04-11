<?php
namespace UniSharp\Cart\Tests\Feature;

use UniSharp\Cart\CartManager;
use UniSharp\Cart\OrderManager;
use UniSharp\Cart\Tests\TestCase;
use UniSharp\Cart\Enums\OrderStatus;
use UniSharp\Pricing\Facades\Pricing;
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
        ]);

        $cart = CartManager::make()->add($product->specs->first(), 1)->save();
        OrderManager::setSerialNumberResolver(function () {
            return 'ABC-1';
        });
        Pricing::shouldReceive('setItems')->andReturnSelf();
        Pricing::shouldReceive('apply')->andReturnSelf();
        Pricing::shouldReceive('getTotal')->andReturn(100);

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

        $this->assertDatabaseHas('orders', [
            'id' => $manager->getOrderInstance()->id,
            'status' => OrderStatus::PENDDING,
            'total_price' => 100,
            'sn' => 'ABC-1'
        ]);

        $this->assertDatabaseHas('order_items', [
            'price' => 20,
            'spec' => 'default',
            'sku' => 'B-1',
            'quantity' => 1
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
    }
}
