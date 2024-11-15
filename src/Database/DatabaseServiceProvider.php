<?php

namespace Navigator\Database;

use Faker\Generator as Faker;
use Navigator\Database\Console\Commands\MakeFactory;
use Navigator\Database\Console\Commands\MakeModel;
use Navigator\Database\Factories\Factory;
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
            MakeFactory::class,
            MakeModel::class,
        ]);

        Factory::setFakerResolver(fn() => $this->app->get(Faker::class));
    }
}
