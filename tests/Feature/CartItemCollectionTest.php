<?php
namespace UniSharp\Cart\Tests\Feature;

use UniSharp\Cart\Cart;
use UniSharp\Cart\Model\CartItem;
use UniSharp\Cart\Tests\TestCase;
use UniSharp\Cart\CartItemCollection;
use UniSharp\Cart\Tests\Fixtures\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use UniSharp\Cart\Tests\Fixtures\Validations\AllRejectValidation;

class CartItemCollectionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();
    }

    public function testAddItems()
    {
        $collection = CartItemCollection::make([
            new CartItem(),
            new CartItem(),
        ]);

        $this->assertCount(2, $collection);
    }

    public function testRejectAllItems()
    {
        config()->set('cart.validations', [
            AllRejectValidation::class,
        ]);

        $collection = CartItemCollection::make([
            new CartItem(),
            new CartItem(),
        ]);

        $this->assertCount(0, $collection);
    }
}
