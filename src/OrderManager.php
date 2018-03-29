<?php
namespace UniSharp\Cart;

use UniSharp\Cart\Models\Order;
use UniSharp\Cart\Models\OrderItem;
use UniSharp\Pricing\Facades\Pricing;

class OrderManager
{
    protected static $serialNumberResolver;
    protected static $pricingResolver;
    protected $order;
    protected $modules = [];

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public static function make(?Order $order = null)
    {
        return new static($order ?? new Order);
    }

    public function getOrderInstance()
    {
        return $this->order;
    }

    public function apply($modules)
    {
        $this->modules = $modules;
        return $this;
    }

    public function checkout(CartItemCollection $items)
    {
        $this->order->sn = $this->order->sn ?? call_user_func(static::$serialNumberResolver);
        $this->order->total_price = $this->getPricing($items)->getTotal();

        $orderItems = $items->map(function ($item) {
            $orderItem = new OrderItem;
            $orderItem->fill($item->only('quentity'));
            $input = collect($item->spec->getAttributes())
                ->except('id', 'created_at', 'updated_at', 'buyable_type', 'buyable_id', 'stock')
                ->mapWithKeys(function ($value, $key) {
                    return [$key == 'name' ? 'spec' : $key => $value];
                })->toArray();
            $orderItem->fill($input);
            $this->order->items()->save($orderItem);
        });

        return $this;
    }

    public function getPricing($items)
    {
        return Pricing::setItems($items)->apply($this->modules);
    }

    public function save()
    {
        $this->order->save();
        return $this;
    }

    public static function setSerialNumberResolver($resolver)
    {
        static::$serialNumberResolver = $resolver;
    }

    public static function setPricingResolver($resolver)
    {
        static::$pricingResolver = $resolver;
    }
}
