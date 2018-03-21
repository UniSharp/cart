<?php
namespace UniSharp\Cart\Tests\Feature\Api;

use UniSharp\Cart\Cart;
use UniSharp\Cart\Tests\TestCase;
use UniSharp\Cart\Tests\Fixtures\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function testStore()
    {
        $product = Product::create([
            'name' => 'ProductA',
            'price' => 50
        ]);

        $response = $this->postJson('/api/v1/carts', [
            'specs' => [
                [
                    'id' => $product->specs->first()->id,
                    'quentity' => 10
                ]
            ]
        ]);

        $response->assertJsonStructure([
            'id', 'items'
        ]);

        $this->assertEquals(
            $product->specs->first()->id,
            collect($response->json()['items'])->first()['id']
        );

        $this->assertEquals(
            10,
            collect($response->json()['items'])->first()['quentity']
        );
    }

    public function testAdd()
    {
        $product = Product::create([
            'name' => 'ProductA',
            'price' => 50
        ]);

        $cart = Cart::create()->getCartInstance();

        $response = $this->putJson("api/v1/carts/{$cart->id}", [
            'specs' =>  [
                [
                    'id' => $product->specs->first()->id,
                    'quentity' => 20
                ]
            ]
        ]);

        $this->assertEquals(
            $product->specs->first()->id,
            collect($response->json()['items'])->first()['id']
        );

        $this->assertEquals(
            20,
            collect($response->json()['items'])->first()['quentity']
        );
    }

    public function testPutAndAutoMerge()
    {
        $product = Product::create([
            'name' => 'ProductA',
            'price' => 50
        ]);

        $cart = Cart::create()->add(
            $product->specs->first()->id,
            10
        )->save()->getCartInstance();

        $response = $this->putJson("api/v1/carts/{$cart->id}", [
            'specs' =>  [
                [
                    'id' => $product->specs->first()->id,
                    'quentity' => 20
                ]
            ]
        ]);

        $this->assertEquals(
            $product->specs->first()->id,
            collect($response->json()['items'])->first()['id']
        );

        $this->assertEquals(
            30,
            collect($response->json()['items'])->first()['quentity']
        );
    }

    public function testRemoveItem()
    {
        $product = Product::create([
            'name' => 'ProductA',
            'price' => 50
        ]);

        $cart = Cart::create()->add(
            $product->specs->first()->id,
            10
        )->save()->getCartInstance();

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'id' => $product->specs->first()->id
        ]);
        $response = $this->delete("api/v1/carts/{$cart->id}/{$product->specs->first()->id}");
        $response->assertSuccessful();
        $this->assertDatabaseMissing('cart_items', [
            'cart_id' => $cart->id,
            'id' => $product->specs->first()->id
        ]);
    }

    public function testDeleteCart()
    {
        $product = Product::create([
            'name' => 'ProductA',
            'price' => 50
        ]);

        $cart = Cart::create()->add(
            $product->specs->first()->id,
            10
        )->save()->getCartInstance();

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'id' => $product->specs->first()->id
        ]);

        $response = $this->delete("api/v1/carts/{$cart->id}");
        $response->assertSuccessful();
        $this->assertDatabaseMissing('carts', [
            'id' => $cart->id,
        ]);
    }
}
