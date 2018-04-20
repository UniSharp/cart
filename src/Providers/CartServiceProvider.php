<?php
namespace UniSharp\Cart\Providers;

use UniSharp\Cart\Models\Cart;
use UniSharp\Cart\Models\Order;
use UniSharp\Cart\OrderManager;
use UniSharp\Cart\Models\CartItem;
use UniSharp\Cart\Models\OrderItem;
use UniSharp\Cart\Enums\OrderStatus;
use UniSharp\Cart\Enums\OrderItemStatus;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use UniSharp\Cart\Enums\ShippingStatus;
use UniSharp\Cart\Contracts\CartContract;
use UniSharp\Cart\Contracts\OrderContract;
use UniSharp\Cart\Contracts\CartItemContract;
use UniSharp\Cart\Contracts\OrderItemContract;
use UniSharp\Cart\Contracts\OrderStatusContract;
use UniSharp\Cart\Contracts\OrderItemStatusContract;
use UniSharp\Cart\Contracts\ShippingStatusContract;

class CartServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(
            __DIR__.'/../../database/migrations'
        );

        OrderManager::setSerialNumberResolver(function () {
            return uniqid();
        });

        $this->app->bind(OrderContract::class, Order::class);
        $this->app->bind(OrderStatusContract::class, OrderStatus::class);
        $this->app->bind(OrderItemStatusContract::class, OrderItemStatus::class);
        $this->app->bind(ShippingStatusContract::class, ShippingStatus::class);
        $this->app->bind(OrderItemContract::class, OrderItem::class);
        $this->app->bind(CartContract::class, Cart::class);
        $this->app->bind(CartItemContract::class, CartItem::class);

        Route::model('order', get_class(resolve(OrderContract::class)));
    }
}
