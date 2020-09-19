<?php

namespace LaravelFilebase;

use Illuminate\Support\ServiceProvider;

use Illuminate\Database\Eloquent\Model;

class LaravelFilebaseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        Model::setConnectionResolver($this->app['db']);

        Model::setEventDispatcher($this->app['events']);
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->resolving('db', function ($db) {
            $db->extend('filebase', function ($config, $name) {
                $config['name'] = $name;
                return new Connection($config);
            });
        });
    }
}
