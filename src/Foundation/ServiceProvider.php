<?php

namespace Navigator\Foundation;

use Navigator\Collections\Arr;
use Navigator\Console\Command;
use Navigator\Console\ConsoleFactory;
use Navigator\Contracts\ServiceProviderInterface;

abstract class ServiceProvider implements ServiceProviderInterface
{
    protected static array $publishes = [];

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

    /** @param array<string, string> $files */
    public function publishes(array $files, string $tag): void
    {
        if ($this->app->runningInConsole()) {
            static::$publishes[$tag] = Arr::merge(static::$publishes[$tag] ?? [], $files);
        }
    }

    /** @return array<string, string> */
    public static function getPublishables(string $tag): array
    {
        return static::$publishes[$tag] ?? [];
    }
}
