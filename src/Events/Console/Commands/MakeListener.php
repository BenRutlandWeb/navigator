<?php

namespace Navigator\Events\Console\Commands;

use Navigator\Console\GeneratorCommand;

class MakeListener extends GeneratorCommand
{
    protected string $type = 'Listener';

    protected string $signature = 'make:listener {name : The listener class}
                                          {--force : Overwrite the listener class if it exists}';

    protected string $description = 'Make a listener class.';

    protected function getStub(): string
    {
        return __DIR__ . '/stubs/listener.stub';
    }

    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . '\\Listeners';
    }
}
