<?php
namespace UniSharp\Cart\Tests\Feature\Api;

use UniSharp\Cart\CartManager;
use UniSharp\Cart\OrderManager;
use UniSharp\Cart\Tests\TestCase;
use Illuminate\Foundation\Auth\User;
use UniSharp\Cart\Enums\OrderStatus;
use UniSharp\Cart\Tests\Fixtures\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function testStore()
    {
        OrderManager::setSerialNumberResolver(function () {
            return 'ABC-1';
        });

        $product = Product::create([
            'name' => 'Product A',
            'price' => 20,
            'sku' => 'B-1',
            'stock' => 20,
        ]);

        $cart = CartManager::make()->add($product->specs->first(), 1)->save();

        $response = $this->postJson('/api/v1/orders', [
            'cart' => $cart->getCartInstance()->id,
            'informations' => [
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
            ]
        ]);

        $response->assertJsonStructure([
            'id', 'items'
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $response->json()['id'],
            'status' => OrderStatus::PENDDING,
            'total_price' => 20,
            'sn' => 'ABC-1'
        ]);

        $this->assertDatabaseHas('order_items', [
            'price' => 20,
            'spec' => 'default',
            'sku' => 'B-1',
            'quentity' => 1
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

    public function testRefresh()
    {
    }

    public function testAdd()
    {
    }

    public function testPutAndAutoMerge()
    {
    }

    public function testPutAndAppendUser()
    {
    }

    public function testRemoveItem()
    {
    }

    public function testDeleteCart()
    {
    }
}
