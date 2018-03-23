<?php

namespace Wqer1019\HotUpdate;

use Illuminate\Routing\Router;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\ServiceProvider;

class HotUpdateServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $config = realpath(__DIR__ . '/../config/update.php');
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'hotUpdate');
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang/zh', 'hotUpdate');

        $this->publishes([
            $config => config_path('update.php'),
            __DIR__ . '/resources/views' => base_path('/resources/views'),
            __DIR__ . '/resources/lang' => base_path('/resources/lang'),
        ]);

        $this->mergeConfigFrom($config, 'update');

        $this->setupRoutes($this->app->router);
    }

    public function setupRoutes(Router $router)
    {
        $router->group(['namespace' => __NAMESPACE__ . '\Controllers'], function ($router) {
            require __DIR__ . '/routes.php';
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(HotUpdate::class, function () {
            $config = config('update');
            $excludeAsset = new ExcludeResource($config['exclude']);

            return new HotUpdate(Finder::create(), $excludeAsset);
        });
    }

    public function provides()
    {
        return [HotUpdate::class];
    }
}
