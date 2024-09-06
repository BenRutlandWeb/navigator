<?php

namespace Navigator\Config;

use Navigator\Foundation\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Repository::class, fn() => new Repository());
    }

    public function boot(): void
    {
        //
    }
}
