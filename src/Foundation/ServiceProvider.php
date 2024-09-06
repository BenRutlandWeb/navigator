<?php

namespace Navigator\Foundation;

use Navigator\Console\Command;
use Navigator\Console\ConsoleFactory;
use Navigator\Contracts\ServiceProviderInterface;

abstract class ServiceProvider implements ServiceProviderInterface
{
    public function __construct(protected Application $app)
    {
        //
    }

    /** @param array<int, class-string<Command>> $commands */
    public function commands(array $commands): void
    {
        if ($this->app->runningInConsole()) {
            $console = $this->app->get(ConsoleFactory::class);

            foreach ($commands as $command) {
                $console->make($command);
            }
        }
    }
}
