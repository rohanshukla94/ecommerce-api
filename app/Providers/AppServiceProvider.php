<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Cart\Cart;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //

        $this->app->singleton(Cart::class, function ($app) {

            if ($app->auth->user()) {
                $app->auth->user()->load([
                    'cart.stock'
                ]);
            }




            return new Cart($app->auth->user());
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
