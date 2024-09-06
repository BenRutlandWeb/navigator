<?php

namespace Navigator\Console\Commands;

use Navigator\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeBlock extends GeneratorCommand
{
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Block';

    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'make:block {name : The block name}
                                       {--force : Overwrite the block if it exists}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Make a block';

    /**
     * Handle further calls
     *
     * @return void
     */
    protected function handle(): void
    {
        parent::handle();

        $title = $this->ask('What is the title?');
        $icon = $this->ask('What is the icon? (without "dashicon-"');

        $dirName = $this->resolveBlockName();

        $stubs = [
            'block.json'       => __DIR__ . '/stubs/block/block-json.stub',
            'template.php'     => __DIR__ . '/stubs/block/template.stub',
        ];

        $dir = get_template_directory() . '/partials/blocks/' . $dirName;

        $this->makeDirectory($dir . '/block.json');

        foreach ($stubs as $fileName => $stubFileName) {
            $stub = $this->files->get($stubFileName);

            $stub = str_replace(
                ['{{ name }}', '{{ title }}', '{{ icon }}'],
                [$dirName, $title, $icon],
                $stub
            );

            $this->files->put($dir . '/' . $fileName, $stub);
        }
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param string $stub The stub contents
     * @param string $name The classname to replace
     *
     * @return string
     */
    protected function replaceClass(string $stub, string $name): string
    {
        $stub = parent::replaceClass($stub, $name);

        return str_replace('{{ name }}', $this->resolveBlockName(), $stub);
    }

    /**
     * Get the block name in kebab-case.
     *
     * @return string
     */
    protected function resolveBlockName()
    {
        return Str::kebab($this->argument('name'));
    }

    /**
     * Get the stub path.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . '/stubs/block/block.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace The root namespace
     *
     * @return string
     */
    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . '\Acf\Blocks';
    }
}
