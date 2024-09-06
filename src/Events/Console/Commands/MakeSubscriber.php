<?php

namespace Navigator\Events\Console\Commands;

use Navigator\Console\GeneratorCommand;

class MakeSubscriber extends GeneratorCommand
{
    protected string $type = 'Subscriber';

    protected string $signature = 'make:subscriber {name : The subscriber class}
                                            {--force : Overwrite the subscriber class if it exists}';

    protected string $description = 'Make a subscriber class.';

    protected function getStub(): string
    {
        return __DIR__ . '/stubs/subscriber.stub';
    }

    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . '\\Listeners';
    }
}
