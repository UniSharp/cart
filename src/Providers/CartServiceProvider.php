<?php
namespace UniSharp\Cart\Providers;

use UniSharp\Cart\Models\Order;
use UniSharp\Cart\OrderManager;
use UniSharp\Cart\Enums\OrderStatus;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use UniSharp\Cart\Enums\ShippingStatus;
use UniSharp\Cart\Contracts\OrderContract;
use UniSharp\Cart\Contracts\OrderItemContract;
use UniSharp\Cart\Contracts\OrderStatusContract;
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
        $this->app->bind(ShippingStatusContract::class, ShippingStatus::class);
        $this->app->bind(OrderItemContract::class, OrderItemStatus::class);

        Route::model('order', get_class(resolve(OrderContract::class)));
    }
}
