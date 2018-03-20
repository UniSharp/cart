<?php
namespace UniSharp\Cart\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(
            __DIR__.'/../../database/migrations'
        );

        Route::prefix('api')
            ->middleware('api')
            ->namespace('UniSharp\Cart\Http\Controllers')
            ->group(__DIR__.'/../../routes/api.php');
    }
}
