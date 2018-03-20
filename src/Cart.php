<?php
namespace UniSharp\Cart;

use ReflectionClass;
use UniSharp\Buyable\Models\Spec;
use UniSharp\Cart\Model\CartItem;
use UniSharp\Buyable\Traits\Buyable;
use Illuminate\Database\Eloquent\Model;
use UniSharp\Cart\Model\Cart as CartModel;
use Illuminate\Database\Eloquent\Relations\Relation;

class Cart
{
    protected $cart;
    protected $items;

    public function __construct(CartModel $cart)
    {
        $this->cart = $cart;
        $this->items = CartItemCollection::make($this->cart->items);
    }

    public static function create(?CartModel $model = null)
    {
        return new static($model ?? CartModel::create());
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
        $this->items->each(function ($item) {
            $item->save();
        });
        return $this;
    }

    public function add($model, $quentity)
    {
        $this->items->push(app(CartItem::class)->fill([
            'id' => $this->getSpecId($model),
            'cart_id' => $this->cart->id,
            'quentity' => $quentity,
        ]));

        return $this;
    }

    public function remove($model)
    {
        $this->items = $this->items->reject(function ($item) use ($model) {
            return $item->id == $this->getSpecId($model);
        });

        return $this;
    }

    public function update($model, $quentity)
    {
        $this->items->where('id', $this->getSpecId($model))->each->fill([
            'cart_id' => $this->cart->id,
            'quentity' => $quentity,
        ]);
        return $this;
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
