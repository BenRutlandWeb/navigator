<?php

namespace Navigator\Validation\Console\Commands;

use Navigator\Console\GeneratorCommand;
use Navigator\Str\Str;

class MakeRule extends GeneratorCommand
{
    protected string $type = 'Rule';

    protected string $signature = 'make:rule {name : The rule class}
                                     {--force : Overwrite the rule if it exists}';

    protected string $description = 'Make a rule class.';

    protected function replaceClass(string $stub, string $name): string
    {
        $stub = parent::replaceClass($stub, $name);

        return Str::replace('{{ key }}', Str::snake($this->argument('name')), $stub);
    }

    protected function getStub(): string
    {
        return __DIR__ . '/stubs/rule.stub';
    }

    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . '\Rules';
    }
}
