<?php
namespace UniSharp\Cart;

use ReflectionClass;
use InvalidArgumentException;
use UniSharp\Buyable\Models\Spec;
use UniSharp\Cart\Models\CartItem;
use Illuminate\Foundation\Auth\User;
use UniSharp\Buyable\Traits\Buyable;
use UniSharp\Cart\Traits\CanPricing;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Model;
use UniSharp\Cart\Models\Cart as CartModel;
use UniSharp\Cart\Contracts\CartItemContract;
use Illuminate\Database\Eloquent\Relations\Relation;

class CartManager
{
    use CanPricing;
    protected static $uuidResolver;
    protected $cart;
    protected $items;

    public function __construct(CartModel $cart)
    {
        $this->cart = $cart;
        $this->items = CartItemCollection::make($this->cart->items);
    }

    public static function make(?CartModel $model = null)
    {
        $id = call_user_func(static::$uuidResolver);

        return new static($model ?? CartModel::create(compact('id')));
    }

    public function assign(User $user)
    {
        if ($this->cart->user_id && $this->cart->user_id != $user->id) {
            throw new InvalidArgumentException();
        }

        $this->cart->user_id = $user->id;
        return $this;
    }

    public function getCartInstance()
    {
        return $this->cart;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function save()
    {
        $this->cart->items()->whereIn(
            'id',
            $this->cart->items()->pluck('id')->diff($this->items->pluck('id'))
        )->delete();
        $this->items->each->save();

        $this->cart->save();
        return $this;
    }

    public function add($model, $quantity, array $extra = [])
    {
        $extra = collect($extra)->except('quantity')->toArray();
        $targetItems = $this->items->where('id', $this->getSpecId($model));
        collect($extra)->each(function ($value, $key) use (&$targetItems) {
            $targetItems = $targetItems->where($key, $value);
        });

        if ($targetItems->count() > 0) {
            $targetItems->each(function ($item) use ($model, $quantity, $extra) {
                $item->quantity += $quantity;
                $item->fill($extra);
            });
        } else {
            $this->items->push(app(CartItemContract::class)->fill([
                'id' => $this->getSpecId($model),
                'cart_id' => $this->cart->id,
                'quantity' => $quantity,
            ] + $extra));
        }

        return $this;
    }

    public function remove($model)
    {
        $this->items = $this->items->reject(function ($item) use ($model) {
            return $item->id == $this->getSpecId($model);
        });

        return $this;
    }

    public function update($model, $quantity)
    {
        $this->items->where('id', $this->getSpecId($model))->each->fill([
            'cart_id' => $this->cart->id,
            'quantity' => $quantity,
        ]);
        return $this;
    }

    public function clean()
    {
        $this->items = CartItemCollection::make();
        return $this;
    }

    public function getPrice()
    {
        return $this->getPricing($this->items)->getTotal();
    }

    public function getOriginalPrice()
    {
        return $this->getPricing($this->items)->getOriginalTotal();
    }

    public function getFee()
    {
        return array_sum($this->getPricing($this->items)->getFees());
    }

    public static function uuidResolver($resolver)
    {
        static::$uuidResolver = $resolver;
    }

    public static function route(callable $callback = null): void
    {
        Route::prefix('carts')->group(function () use ($callback) {
            $namespace = '\\UniSharp\\Cart\\Http\\Controllers\\Api\\V1\\';

            Route::post('/', $namespace . 'CartsController@store');
            Route::post('/{cart}', $namespace . 'CartsController@refresh');
            Route::put('/{cart}', $namespace . 'CartsController@update');
            Route::get('/{cart}', $namespace . 'CartsController@show');
            Route::delete('/{cart}/{item}', $namespace . 'CartsController@delete');
            Route::delete('/{cart}/', $namespace . 'CartsController@destroy');

            if ($callback) {
                $callback();
            }
        });
    }

    protected function getSpecId($model)
    {
        switch (true) {
            case $this->isSpec($model):
                return $model->id;
                break;
            case $this->isBuyable($model) && $spec = $model->getSpecifiedSpec():
                return $spec->id;
                break;
            case is_numeric($model):
                return $model;
                break;
            default:
                throw new \InvalidArgumentException("this buyable model dosen't have spec");
                break;
        }
    }

    protected function isBuyable($model)
    {
        return $model instanceof Model && array_key_exists(Buyable::class, (new ReflectionClass($model))->getTraits());
    }

    protected function isSpec($model)
    {
        return $model instanceof Spec;
    }
}
