<?php
namespace UniSharp\Cart\Tests\Feature;

use UniSharp\Cart\CartManager;
use UniSharp\Cart\OrderManager;
use UniSharp\Cart\Tests\TestCase;
use UniSharp\Cart\Tests\Fixtures\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use UniSharp\Pricing\Facades\Pricing;

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

        $manager = OrderManager::make()->checkout($cart->getItems())->save();
        $this->assertDatabaseHas('orders', [
            'id' => $manager->getOrderInstance()->id,
            'total_price' => 100,
            'sn' => 'ABC-1'
        ]);

        $this->assertDatabaseHas('order_items', [
            'price' => 20,
            'spec' => 'default',
            'sku' => 'B-1',
            'quentity' => 1
        ]);
    }
}
