<?php
namespace UniSharp\Cart\Tests;

use UniSharp\Pricing\PricingServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use UniSharp\Cart\Providers\CartServiceProvider;
use UniSharp\Buyable\Providers\BuyableServiceProvider;
use UniSharp\Cart\Tests\Fixtures\Providers\TestingServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'test');
        $app['config']->set('database.connections.test', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }


    protected function getPackageProviders($app)
    {
        return [
            PricingServiceProvider::class,
            BuyableServiceProvider::class,
            CartServiceProvider::class,
            TestingServiceProvider::class,
        ];
    }
}
