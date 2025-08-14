<?php

namespace Navigator\Console;

use Navigator\Console\Commands\MakeCommand;
use Navigator\Foundation\Application;
use Navigator\Foundation\BootstrapManager;
use Navigator\Foundation\ServiceProvider;

class ConsoleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ConsoleFactory::class, fn(Application $app) => new ConsoleFactory($app));
    }

    public function boot(): void
    {
        $manifest = $this->app->make(BootstrapManager::class);

        $commands = [
            MakeCommand::class,
        ];

        $this->commands(array_merge($commands, $manifest->get(Command::class)));
    }
}
