<?php
namespace UniSharp\Cart\Tests\Feature;

use UniSharp\Cart\Cart;
use UniSharp\Cart\Tests\TestCase;
use UniSharp\Cart\Tests\Fixtures\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function testAddSpec()
    {
        $product = Product::create([
            'name' => 'abc',
            'price' => 4,
        ]);

        $cart = Cart::create()->add($product->specs->first(), 20)->save();
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->getCartInstance()->id,
            'id' =>  $product->getSpecifiedSpec()->id,
            'quentity' => 20 ,
        ]);
    }

    public function testAddBuyableModel()
    {
        $product = Product::create([
            'name' => 'abc',
            'price' => 4,
        ]);

        $cart = Cart::create()->add($product, 20)->save();
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->getCartInstance()->id,
            'id' =>  $product->getSpecifiedSpec()->id,
            'quentity' => 20,
        ]);
    }

    public function testAddBySpecId()
    {
        $product = Product::create([
            'name' => 'abc',
            'price' => 4,
        ]);

        $cart = Cart::create()->add($product->specs->first()->id, 20)->save();
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->getCartInstance()->id,
            'id' =>  $product->getSpecifiedSpec()->id,
            'quentity' => 20 ,
        ]);
    }

    public function testUpdateSpec()
    {
        $product = Product::create([
            'name' => 'abc',
            'price' => 4,
        ]);

        $cart = Cart::create()->add($product->specs->first(), 20)->save();
        $cart->update($product->getSpecifiedSpec(), 9)->save();
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->getCartInstance()->id,
            'id' =>  $product->getSpecifiedSpec()->id,
            'quentity' => 9,
        ]);
    }

    public function testUpdateBuyableModel()
    {
        $product = Product::create([
            'name' => 'abc',
            'price' => 4,
        ]);

        $cart = Cart::create()->add($product, 20);
        $cart->update($product->getSpecifiedSpec(), 9)->save();
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->getCartInstance()->id,
            'id' =>  $product->getSpecifiedSpec()->id,
            'quentity' => 9,
        ]);
    }

    public function testRemove()
    {
        $product = Product::create([
            'name' => 'abc',
            'price' => 4,
        ]);

        $cart = Cart::create()->add($product, 20);

        $cart->remove($product->specs()->first())->save();
        $this->assertDatabaseMissing('cart_items', [
            'cart_id' => $cart->getCartInstance()->id,
            'id' =>  $product->getSpecifiedSpec()->id,
        ]);
    }
}
