<?php

namespace Navigator\Foundation;

use Faker\Factory;
use Faker\Generator as Faker;
use Navigator\Foundation\Console\Commands\GetEnvironment;
use Navigator\Foundation\Console\Commands\ListCommands;
use Navigator\Foundation\Console\Commands\MakeProvider;
use Navigator\Foundation\Console\Commands\Publish;
use Navigator\Foundation\Console\Commands\SetEnvironment;
use Navigator\Foundation\ServiceProvider;

class FoundationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Mix::class, fn(Application $app) => new Mix($app->assetUrl()));

        $this->app->bind(Faker::class, function (Application $app, ?string $locale = null) {
            $generator = Factory::create($locale ?? get_locale());

            $generator->addProvider(new FakerProvider($generator));

            return $generator;
        });
    }

    public function boot(): void
    {
        $this->commands([
            GetEnvironment::class,
            MakeProvider::class,
            ListCommands::class,
            Publish::class,
            SetEnvironment::class,
        ]);
    }
}
