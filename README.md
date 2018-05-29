# UniSharp Cart

Let buyable item can add to cart,
and make order with cart's items,
and also provide payment feature.

This package depends on:

- [unisharp/buyable](https://github.com/UniSharp/buyable)
- [unisharp/pricing](https://github.com/UniSharp/pricing)
- [voicetube/taiwan-payment-gateway](https://github.com/voicetube/Taiwan-Payment-Gateway)
- [unisharp/payment](https://github.com/UniSharp/unisharp-payment)

## Installation

```composer require unisharp/cart dev-master```

## Cart Usages

### Use Api

Include api into ```api.php```

```php
Route::group(['prefix' => 'v1'], function () {
    CartManager::route();
});
```

route lists:

| Method | Uri                        | Comment                                  |
|:-------|:---------------------------|:-----------------------------------------|
| POST   | api/v1/carts               | Create the cart                          |
| DELETE | api/v1/carts/{cart}        | Delete the cart and cart's items         |
| GET    | api/v1/carts/{cart}        | Get the cart and cart's items            |
| PUT    | api/v1/carts/{cart}        | Add item(s) to the cart                  |
| POST   | api/v1/carts/{cart}        | Refresh cart and add item(s) to the cart |
| DELETE | api/v1/carts/{cart}/{item} | Remove a item from the cart              |

### Use CartManager

Create a new cart

```php
$cart = CartManager::make();
```

Get a exist cart

```php
$cart = CartManager::make($cart);
```

Add item to the cart

```php
$item = new Item([
    'id' => 1,
    '$quantity' => 10,
    'extra' => [
        'comment' => '...'
    ]
]);

$cart->add($item->id, $item->quantity, $item->extra)->save();
```

Get cart's items

```php
$cart->getCartInstance()->getItems();
```

Remove item from the cart

```php
$cart->remove($item)->save();
```

Clean cart's items

```php
$cart->clean();
```

Destroy the cart

```php
$cart->delete();
```

## Order Usages

### Use Api

Include api into ```api.php```

```php
Route::group(['prefix' => 'v1'], function () {
    OrderManager::route();
});
```

route lists:

| Method | Uri                          | Comment                                              |
|:-------|:-----------------------------|:-----------------------------------------------------|
| POST   | api/v1/orders                | Create an order                                      |
| GET    | api/v1/orders                | List all orders                                      |
| DELETE | api/v1/orders/{order}        | Delete the order                                     |
| PUT    | api/v1/orders/{order}        | Update the order's status, price and shipping_status |
| GET    | api/v1/orders/{order}        | Get the order                                        |
| DELETE | api/v1/orders/{order}/{item} | Remove a item from the order                         |
| GET    | api/v1/user/me/order-items   | Get current user's orders                            |

### Use OrderManager

Create an order manager

```php
// Get order manager
$order = OrderManager::make();

// Assign operator
$order->assign(auth()->user());

// Checkout cart's items and buyer and receiver's information
$items = CartManager::make($cart)->getItems();
$information = [
    'buyer' => [],
    'receiver' => [],
    'payment' => 'credit'
];
$order->checkout(items, informations)
```

Get an exist order

```php
$order = OrderManager::make($order)->getOrderInstance();
```

# Pricing Usages

Both of CartManager and OrderManager already have trait

```php
class CartManager
{
    use CanPricing;
    ...
}
```

Customize pricing module

```php
use UniSharp\Pricing\Pricing;
use UniSharp\Pricing\ModuleContract;

class CustomPricingModule implements ModuleContract
{
    public function handle(Pricing $pricing, Closure $next)
    {
        ...
        return $next($pricing);
    }

    public function finish(Pricing $pricing)
    {
        ...
    }
}
```

Set Custom pricing module in `config/pricing.php`

```php
return [
    'modules' => [
        CustomPricingModule::class
    ],
];
```

Get pricing

```php
// get original price
$cart->getOriginalPrice();

// get total price
$cart->getPrice();

// get fee
$cart->getFee();
```

More details on [unisharp/pricing](https://github.com/UniSharp/pricing)

## Payment Usages

Set payment gateway config

```php
return [
    'payment' => [
        'driver'         => 'EcPay',
        'merchantId'     => '2000132',
        'hashKey'        => '5294y06JbISpM5x9',
        'hashIV'         => 'v77hoKGq4kWxNNIS',
        'actionUrl'      => 'https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/',
        'returnUrl'      => 'https://localhost/payment/confirm',
        'notifyUrl'      => 'https://localhost/payment/notify',
        'clientBackUrl'  => 'https://localhost/payment/return',
        'paymentInfoUrl' => 'https://localhost/payment/information' 
    ],
]
```

Set api into ```api.php```

```php
// Include payment api
Route::group(['prefix' => 'v1'], function () {
    OrderManager::route();
});

// Implement payment response url
Route::group(['prefix' => 'v1/payment'], function () {
    Route::get('confirm', function () {...});
    Route::get('notify', function () {...});
    Route::get('return', function () {...});
    Route::get('information', function () {...});
});
```

| Method | Uri                          | Comment                 |
|:-------|:-----------------------------|:------------------------|
| GET    | api/v1/orders/{order}/pay    | Call payment            |
| GET    | api/v1/payment/confirm       | Payment completed url   |
| GET    | api/v1/payment/notify        | Notify url              |
| GET    | api/v1/payment/return        | Client back url         |
| GET    | api/v1/payment/information   | Payment Information url |
