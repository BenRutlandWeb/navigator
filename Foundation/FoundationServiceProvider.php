<?php

namespace Navigator\Foundation;

use Navigator\Foundation\Console\Commands\GetEnvironment;
use Navigator\Foundation\Console\Commands\ListCommands;
use Navigator\Foundation\Console\Commands\MakeProvider;
use Navigator\Foundation\Console\Commands\SetEnvironment;
use Navigator\Foundation\ServiceProvider;

class FoundationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            Mix::class,
            fn (Application $app) => new Mix($app->config('app.asset_url'))
        );
    }

    public function boot(): void
    {
        $this->commands([
            GetEnvironment::class,
            MakeProvider::class,
            ListCommands::class,
            SetEnvironment::class,
        ]);
    }
}
