<?php

namespace Georgie\AutoCreate;

use Georgie\AutoCreate\Commands\AuthCommand;
use Georgie\AutoCreate\Commands\AutoCreateCommand;
use Georgie\AutoCreate\Commands\AutoNameCreateCommand;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public $singletons = [];

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                AutoCreateCommand::class,
                AutoNameCreateCommand::class,
                AuthCommand::class,
            ]);
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('GAutoCreate', function () {
            return new Provider();
        });
    }
}
