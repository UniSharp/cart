<?php
namespace UniSharp\Cart\Tests;

use UniSharp\Cart\CartManager;
use UniSharp\Cart\OrderManager;
use Illuminate\Support\Facades\Route;
use UniSharp\Pricing\PricingServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use UniSharp\Cart\Providers\CartServiceProvider;
use UniSharp\Buyable\Providers\BuyableServiceProvider;
use Askedio\SoftCascade\Providers\GenericServiceProvider;
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

        Route::prefix('/api/v1')->middleware('api')->group(function () {
            CartManager::route();
            OrderManager::route();
        });
    }

    protected function getPackageProviders($app)
    {
        return [
            PricingServiceProvider::class,
            BuyableServiceProvider::class,
            CartServiceProvider::class,
            GenericServiceProvider::class,
            TestingServiceProvider::class,
        ];
    }
}
