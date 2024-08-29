<?php

namespace Navigator\Routing\Console\Commands;

use Navigator\Console\GeneratorCommand;

class MakeController extends GeneratorCommand
{
    protected string $type = 'Controller';

    protected string $signature = 'make:controller {name : The name of controller}
                                       {--force : Overwrite the model if it exists}';

    protected string $description = 'Make a controller class.';

    protected function getStub(): string
    {
        return __DIR__ . '/stubs/controller.stub';
    }

    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . '\\Http\\Controllers';
    }
}
