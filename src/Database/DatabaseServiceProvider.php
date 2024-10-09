<?php

namespace Navigator\Database;

use Navigator\Database\Console\Commands\FlushPermalinks;
use Navigator\Database\Console\Commands\MakeModel;
use Navigator\Foundation\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Connection::class, function () {
            global $wpdb;
            return new Connection($wpdb);
        });
    }

    public function boot(): void
    {
        $this->commands([
            FlushPermalinks::class,
            MakeModel::class,
        ]);
    }
}
