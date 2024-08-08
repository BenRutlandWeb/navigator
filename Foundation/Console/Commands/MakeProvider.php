<?php

namespace Navigator\Foundation\Console\Commands;

use Navigator\Console\GeneratorCommand;

class MakeProvider extends GeneratorCommand
{
    protected string $type = 'Provider';

    protected string $signature = 'make:provider {name : The provider class}
                                          {--force : Overwrite the provider if it exists}';

    protected string $description = 'Make a provider class.';

    protected function getStub(): string
    {
        return __DIR__ . '/stubs/provider.stub';
    }

    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . '\Providers';
    }
}
