<?php

namespace Georgie\AutoCreate;

use Georgie\AutoCreate\Commands\AutoCreateCommand;
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
