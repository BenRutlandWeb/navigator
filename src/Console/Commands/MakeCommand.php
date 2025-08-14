<?php

namespace Navigator\Console\Commands;

use Navigator\Console\GeneratorCommand;
use Navigator\Str\Str;

class MakeCommand extends GeneratorCommand
{
    protected string $type = 'Command';

    protected string $signature = 'make:command {name : The name of the command}
                                       {--force : Overwrite the command if it exists}';

    protected string $description = 'Make a command class.';

    protected function replaceClass(string $stub, string $name): string
    {
        $stub = parent::replaceClass($stub, $name);

        return Str::replace('{{ name }}', Str::kebab($this->argument('name')), $stub);
    }

    protected function getStub(): string
    {
        return __DIR__ . '/stubs/command.stub';
    }

    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . '\\Commands';
    }
}
