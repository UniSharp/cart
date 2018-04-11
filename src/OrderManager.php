<?php
namespace UniSharp\Cart;

use UniSharp\Cart\Models\Order;
use UniSharp\Cart\Models\OrderItem;
use UniSharp\Cart\Models\Information;
use UniSharp\Pricing\Facades\Pricing;
use UniSharp\Cart\Contracts\OrderContract;
use UniSharp\Cart\Contracts\OrderStatusContract;

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

    public static function make(?OrderContract $order = null)
    {
        return new static($order ?? app(OrderContract::class));
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

    public function checkout(CartItemCollection $items, array $informations = [])
    {
        $this->order->status = app(OrderStatusContract::class);
        $this->order->sn = $this->order->sn ?? call_user_func(static::$serialNumberResolver);
        $this->order->total_price = $this->getPricing($items)->getTotal();
        $this->order->save();

        $this->saveCartItems($items);

        if (array_key_exists('receiver', $informations)) {
            $this->saveReceiverInformation($informations['receiver']);
        }

        if (array_key_exists('buyer', $informations)) {
            $this->saveBuyerInformation($informations['buyer']);
        }

        return $this;
    }

    public function getPricing($items)
    {
        $pricing = Pricing::setItems($items);
        collect($this->modules)->each(function ($moduel) use ($pricing) {
            $pring->apply($pricing);
        });

        return $pricing;
    }

    public static function setSerialNumberResolver($resolver)
    {
        static::$serialNumberResolver = $resolver;
    }

    public static function setPricingResolver($resolver)
    {
        static::$pricingResolver = $resolver;
    }

    protected function saveCartItems(CartItemCollection $items)
    {
        $items->each(function ($item) {
            $orderItem = new OrderItem($item->only('quantity'));
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

    protected function saveReceiverInformation(array $information)
    {
        $information['type'] = 'receiver';
        $info = $this->order->receiverInformation()->create($information);
        return $this;
    }

    protected function saveBuyerInformation(array $information)
    {
        $information['type'] = 'buyer';
        $this->order->buyerInformation()->create($information);
        return $this;
    }
}
