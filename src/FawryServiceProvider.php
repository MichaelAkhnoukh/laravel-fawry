<?php


namespace Caishni\Fawry;

use Illuminate\Support\ServiceProvider;

class FawryServiceProvider extends ServiceProvider
{
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/fawry.php' => config_path('fawry.php')
            ],'config');
        }

        $this->app->bind('fawrypay', function ($app) {
            $config = $app->config['fawry'];
            return new FawryPay($config);
        });
    }

    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/fawry.php',
            'fawry');
    }
}