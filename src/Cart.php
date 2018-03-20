<?php
namespace UniSharp\Cart;

use ReflectionClass;
use UniSharp\Buyable\Models\Spec;
use UniSharp\Buyable\Traits\Buyable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use UniSharp\Cart\Model\Cart as CartModel;

class Cart
{
    protected $cart;

    public function __construct(CartModel $cart)
    {
        $this->cart = $cart;
    }

    public static function create(?CartModel $model = null)
    {
        return new static($model ?? CartModel::create());
    }

    public function getCartInstance()
    {
        return $this->cart;
    }

    public function add($model, $quentity)
    {
        $this->cart->items()->create([
            'id' => $this->getSpecId($model),
            'cart_id' => $this->cart->id,
            'quentity' => $quentity,
        ]);

        return $this;
    }

    public function remove($model)
    {
        $this->cart->items()->where('id', $this->getSpecId($model))->delete();
        return $this;
    }

    public function update($model, $quentity)
    {
        $this->cart->items()->where('id', $this->getSpecId($model))->update([
            'quentity' => $quentity,
        ]);

        return $this;
    }

    protected function getSpecId(Model $model)
    {
        switch (true) {
            case $this->isSpec($model):
                return $model->id;
                break;
            case $this->isBuyable($model) && $spec = $model->getSpecifiedSpec():
                return $spec->id;
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
