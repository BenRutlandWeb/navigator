<?php

namespace Navigator\Config;

use Navigator\Filesystem\Filesystem;
use Navigator\Foundation\Application;
use Navigator\Foundation\ServiceProvider;
use Throwable;

class ConfigServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Repository::class, function (Application $app) {
            try {
                $config = $app->get(Filesystem::class)->requireOnce($app->path('config.php'));
            } catch (Throwable $e) {
                $config = [];
            }

            return new Repository($config);
        });
    }

    public function boot(): void
    {
        //
    }
}
