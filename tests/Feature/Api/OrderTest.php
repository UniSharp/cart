<?php
namespace UniSharp\Cart\Tests\Feature\Api;

use UniSharp\Cart\CartManager;
use UniSharp\Cart\Models\Order;
use UniSharp\Cart\OrderManager;
use UniSharp\Cart\Tests\TestCase;
use UniSharp\Cart\Models\OrderItem;
use Illuminate\Foundation\Auth\User;
use UniSharp\Cart\Enums\OrderStatus;
use UniSharp\Cart\Tests\Fixtures\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
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
            'receiver_information' => [
                'name' => 'User A',
                'address' => 'A 區 B 縣',
                'phone' => '0912345678',
                'email' => 'fk@example.com'
            ],
            'buyer_information' => [
                'name' => 'User A',
                'address' => 'A 區 B 縣',
                'phone' => '0912345678',
                'email' => 'fk@example.com'
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

    public function testStoreAfterLogin()
    {
        $this->actingAs($user = User::create());
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
            'receiver_information' => [
                'name' => 'User A',
                'address' => 'A 區 B 縣',
                'phone' => '0912345678',
                'email' => 'fk@example.com'
            ],
            'buyer_information' => [
                'name' => 'User A',
                'address' => 'A 區 B 縣',
                'phone' => '0912345678',
                'email' => 'fk@example.com'
            ]
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $response->json()['id'],
            'user_id' => $user->id
        ]);
    }

    public function testList()
    {
        Order::create([
            'sn' => 'ABC-1',
            'status' => OrderStatus::COMPLETED,
            'total_price' => 100,
        ])->items()->save(OrderItem::create([
            'spec' => 'default',
            'price' => 100
        ]));

        $response = $this->get('/api/v1/orders');
        $response->assertJsonStructure([
            'data' => [
                '*' =>[
                    'id',
                    'status',
                    'shipping_status',
                    'items',
                    'receiver_information',
                    'buyer_information',
                ]
            ]
        ]);
    }
    public function testUpdate()
    {
        $order = Order::create([
            'sn' => 'ABC-1',
            'status' => OrderStatus::COMPLETED,
            'total_price' => 100,
        ]);

        $order->items()->saveMany([
            $item1 = OrderItem::create([
                'spec' => 'default',
                'quantity' => 1,
                'price' => 100
            ]),
            $item2 = OrderItem::create([
                'spec' => 'default',
                'quantity' => 2,
                'price' => 20
            ]),
        ]);

        $order->receiverInformation()->create([
            'type' => 'receiver',
            'phone' => '999'
        ]);

        $response = $this->putJson("/api/v1/orders/{$order->id}", [
            'receiver_information' => [
                'phone' => '12345',
            ],
            'items' => [
                [
                    'id' => $item1->id,
                    'quantity' =>  2
                ]
            ]
        ]);

        $response->assertSuccessful();

        $this->assertDatabaseHas('order_items', [
            'id' => $item1->id,
            'quantity' => 2
        ]);

        $this->assertDatabaseMissing('order_items', [
            'id' => $item2->id
        ]);

        $this->assertEquals('12345', $order->refresh()->receiverInformation->phone);
    }

    public function testShow()
    {
        $order = Order::create([
            'sn' => 'ABC-1',
            'status' => OrderStatus::COMPLETED,
            'total_price' => 100,
        ]);
        $order->items()->save($item = OrderItem::create([
            'spec' => 'default',
            'price' => 100
        ]));

        $response = $this->get("/api/v1/orders/{$order->id}");
        $response->assertSuccessful();
        $this->assertEquals(
            $order->refresh()->load('items', 'receiverInformation', 'buyerInformation')->toArray(),
            $response->json()
        );
    }

    public function testDeleteOrder()
    {
        $order = Order::create([
            'sn' => 'ABC-1',
            'status' => OrderStatus::COMPLETED,
            'total_price' => 100,
        ])->items()->save($item = OrderItem::create([
            'spec' => 'default',
            'price' => 100
        ]));

        $response = $this->delete("/api/v1/orders/{$order->id}");
        $this->assertDatabaseMissing('orders', ['id'  => $order->id]);
    }
}
