<?php

namespace MikiBabi\YagoutPay;

use Illuminate\Support\ServiceProvider;
use MikiBabi\YagoutPay\Yagout;


class YagoutPayServiceProvider extends ServiceProvider
{
    public function boot()
    {
  

        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
         $this->loadViewsFrom(__DIR__.'/resources/views', 'yagoutpay');


        $this->publishes([
            __DIR__.'/../config/yagoutpay.php' => config_path('yagoutpay.php'),
        ], 'yagoutpay-config');

        
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/yagoutpay.php',
            'yagoutpay'
        );

        // 🔹 Add this binding so the facade works
        $this->app->singleton('yagout', function ($app) {
            return new Yagout();
        });
    }
}
