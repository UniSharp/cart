<?php
namespace UniSharp\Cart\Providers;

use Gateway;
use UniSharp\Cart\Models\Cart;
use UniSharp\Cart\Models\Order;
use UniSharp\Cart\OrderManager;
use UniSharp\Cart\Enums\Payment;
use UniSharp\Cart\Models\CartItem;
use UniSharp\Cart\Models\OrderItem;
use UniSharp\Cart\Enums\OrderStatus;
use Illuminate\Support\Facades\Route;
use UniSharp\Cart\Enums\PaymentStatus;
use Illuminate\Support\ServiceProvider;
use UniSharp\Cart\Enums\ShippingStatus;
use UniSharp\Payment\Factories\Gateway as PaymentGateway;
use UniSharp\Payment\Factories\Response as PaymentResponse;
use UniSharp\Cart\Enums\OrderItemStatus;
use UniSharp\Cart\Contracts\CartContract;
use UniSharp\Cart\Contracts\OrderContract;
use UniSharp\Cart\Contracts\PaymentContract;
use UniSharp\Cart\Contracts\CartItemContract;
use UniSharp\Cart\Contracts\OrderItemContract;
use UniSharp\Cart\Contracts\OrderStatusContract;
use UniSharp\Cart\Contracts\PaymentStatusContract;
use UniSharp\Cart\Contracts\ShippingStatusContract;
use UniSharp\Cart\Contracts\OrderItemStatusContract;
use VoiceTube\TaiwanPaymentGateway\TaiwanPaymentResponse;
use VoiceTube\TaiwanPaymentGateway\Common\GatewayInterface;
use VoiceTube\TaiwanPaymentGateway\Common\ResponseInterface;

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
        $this->app->bind(PaymentStatusContract::class, PaymentStatus::class);
        $this->app->bind(PaymentContract::class, Payment::class);


        $this->app->bind(GatewayInterface::class, function () {
            return PaymentGateway::create(config('cart.payment.driver', 'SpGateway'), [
                'hashKey'        => config('cart.payment.hashKey'),
                'hashIV'         => config('cart.payment.hashIV'),
                'merchantId'     => config('cart.payment.merchantId'),
                // 'actionUrl'      => 'https://ccore.spgateway.com/MPG/mpg_gateway',
                'returnUrl'      => route('payment.callback'), // config('cart.payment.returnUrl'),
                'notifyUrl'      => route('payment.callback'),
                'clientBackUrl'  => config('cart.payment.clientBackUrl'),
                'paymentInfoUrl' => config('cart.payment.paymentInfoUrl')
            ]);
        });

        $this->app->bind(ResponseInterface::class, function () {
            return PaymentResponse::create(config('cart.payment.driver', 'SpGateway'), [
                'hashKey'        => config('cart.payment.hashKey'),
                'hashIV'         => config('cart.payment.hashIV'),
                'merchantId'     => config('cart.payment.merchantId'),
            ]);
        });

        Route::model('order', get_class(resolve(OrderContract::class)));
    }
}
