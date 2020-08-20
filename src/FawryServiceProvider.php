<?php


namespace Caishni\Fawry;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\ServiceProvider;

class FawryServiceProvider extends ServiceProvider
{
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/fawry.php' => config_path('fawry.php')
            ], 'config');

            if (!class_exists('CreateUserCardsTable')) {
                $this->publishes([
                    __DIR__ . '/../database/migrations/create_user_cards_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_user_cards_table.php'),
                ], 'migrations');
            }
        }

        $this->app->bind('fawrypay', function ($app) {
            $config = $app->config['fawry'];
            return new FawryPay($config);
        });
    }

    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/fawry.php',
            'fawry');

        Collection::macro('flattenTree', function ($childrenField) {
            $result = collect();

            foreach ($this->items as $item) {
                $result->push($item);

                if ($item->$childrenField instanceof Collection) {
                    $result = $result->merge($item->$childrenField->flattenTree($childrenField));
                    $item->unsetRelations($childrenField);
                }
            }

            return $result;
        });

        Collection::macro('mapIntoItems', function () {
            return $this->mapInto(Item::class);
        });
    }
}