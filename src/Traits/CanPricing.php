<?php
namespace UniSharp\Cart\Traits;

use UniSharp\Pricing\Facades\Pricing;
use Illuminate\Support\Facades\Config;

trait CanPricing
{
    protected $modules = [];

    public function getModules()
    {
        return Config::get('cart.pricing.modules', []);
    }

    public function getPricing($items)
    {
        $pricing = Pricing::setItems($items);
        collect($this->getModules())->each(function ($module) use ($pricing) {
            $pring->apply($module);
        });

        return $pricing;
    }
}
