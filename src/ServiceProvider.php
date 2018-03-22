<?php

namespace Wqer1019\AutoUpdate;

use Illuminate\Routing\Router;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $config = realpath(__DIR__ . '/../config/update.php');
        $this->loadTranslationsFrom(__DIR__ . 'resources/lang/zh', 'wqer1019');

        $this->publishes([
            $config => config_path('update.php'),
            __DIR__ . '/resources/views' => base_path('resources/views'),
            __DIR__ . '/resources/lang' => base_path('resources/lang'),
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
        $this->app->singleton(AutoUpdate::class, function () {
            $config = config('update');
            $excludeAsset = new ExcludeResource($config['exclude']);
            return new AutoUpdate(Finder::create(), $excludeAsset);
        });
    }

    public function provides()
    {
        return [AutoUpdate::class];
    }
}