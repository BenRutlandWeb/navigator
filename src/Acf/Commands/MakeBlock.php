<?php

namespace Navigator\Acf\Console\Commands;

use Navigator\Console\GeneratorCommand;
use Navigator\Str\Str;

class MakeBlock extends GeneratorCommand
{
    protected string $type = 'Block';

    protected string $signature = 'make:block {name : The name of the block}
                                       {--force : Overwrite the block if it exists}';

    protected string $description = 'Make a block class.';

    protected function handle(): void
    {
        parent::handle();

        $dirName = $this->resolveBlockName();

        $stubs = [
            'block.json'   => __DIR__ . '/stubs/block-json.stub',
            'template.php' => __DIR__ . '/stubs/template.stub',
        ];

        $dir = $this->app->path('resources/blocks/' . $dirName);

        $this->makeDirectory($dir . '/block.json');

        foreach ($stubs as $fileName => $stubFileName) {
            $stub = $this->files->get($stubFileName);

            $stub = str_replace(
                ['{{ name }}', '{{ title }}'],
                [$dirName, Str::headline($dirName)],
                $stub
            );

            $this->files->put($dir . '/' . $fileName, $stub);
        }
    }

    protected function resolveBlockName(): string
    {
        return Str::kebab($this->argument('name'));
    }

    protected function replaceClass(string $stub, string $name): string
    {
        $stub = parent::replaceClass($stub, $name);

        return str_replace('{{ name }}', $this->resolveBlockName(), $stub);
    }

    protected function getStub(): string
    {
        return __DIR__ . '/stubs/block.stub';
    }

    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . '\\Acf\\Blocks';
    }
}
