<?php

namespace Navigator\Acf\Console\Commands;

use Navigator\Console\GeneratorCommand;

class MakeFieldGroup extends GeneratorCommand
{
    protected string $type = 'FieldGroup';

    protected string $signature = 'make:field-group {name : The name of the field group}
                                       {--force : Overwrite the field group if it exists}';

    protected string $description = 'Make a field group class.';

    protected function getStub(): string
    {
        return __DIR__ . '/stubs/field-group.stub';
    }

    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . '\\Acf\\FieldGroups';
    }
}
