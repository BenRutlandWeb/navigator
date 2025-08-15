<?php

namespace Navigator\Cache;

use Navigator\Cache\Console\Commands\CacheClear;
use Navigator\Cache\Console\Commands\CacheForget;
use Navigator\Database\Connection;
use Navigator\Foundation\Application;
use Navigator\Foundation\ServiceProvider;

class CacheServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Repository::class, fn(Application $app) => new Repository(
            $app->get(Connection::class)
        ));
    }

    public function boot(): void
    {
        $this->commands([
            CacheClear::class,
            CacheForget::class,
        ]);
    }
}
