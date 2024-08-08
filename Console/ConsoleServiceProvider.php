<?php

namespace Navigator\Console;

use Navigator\Foundation\Application;
use Navigator\Foundation\ServiceProvider;

class ConsoleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ConsoleFactory::class, fn (Application $app) => new ConsoleFactory($app));
    }

    public function boot(): void
    {
        //
    }
}
