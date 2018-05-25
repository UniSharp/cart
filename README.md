# UniSharp Cart

let buyable item can add to cart

## Installation

```composer require unisharp/cart dev-master```

## Usages

### Use Api

Include router

```php
CartManager::route();
```

route lists:

| Method | Uri                        | Comment                                  |
|:------:|:--------------------------:|:----------------------------------------:|
| POST   | api/v1/carts               | Create the cart                          |
| DELETE | api/v1/carts/{cart}        | Delete the cart and cart's items         |
| GET    | api/v1/carts/{cart}        | Get the cart and cart's items            |
| PUT    | api/v1/carts/{cart}        | Add item(s) to the cart                  |
| POST   | api/v1/carts/{cart}        | Refresh cart and add item(s) to the cart |
| DELETE | api/v1/carts/{cart}/{item} | Remove a item from the cart              |

### Use Model

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

Get the cart and cart's items

```php
$cart->getCartInstance()->load('items');
```

Remove item from the cart

```php
$cart->remove($item)->save();
```

Destroy the cart

```php
$cart->delete();
```
