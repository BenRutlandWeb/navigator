<?php

namespace Navigator\Cache;

use Navigator\Database\Connection;
use Navigator\Foundation\Application;
use Navigator\Foundation\ServiceProvider;

class CacheServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Repository::class, fn(Application $app) => new Repository(
            $app->get(Connection::class),
            $app->env('CACHE_PREFIX', '')
        ));
    }

    public function boot(): void
    {
        //
    }
}
