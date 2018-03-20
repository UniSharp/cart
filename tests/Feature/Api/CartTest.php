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
}
