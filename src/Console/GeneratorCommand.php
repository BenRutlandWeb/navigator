<?php

namespace Navigator\Console;

use Navigator\Collections\Arr;
use Navigator\Console\Command;
use Navigator\Filesystem\Filesystem;
use Navigator\Foundation\Application;
use Navigator\Str\Str;

abstract class GeneratorCommand extends Command
{
    protected Filesystem $files;

    protected string $type;

    protected array $reservedNames = [
        '__halt_compiler',
        'abstract',
        'and',
        'array',
        'as',
        'break',
        'callable',
        'case',
        'catch',
        'class',
        'clone',
        'const',
        'continue',
        'declare',
        'default',
        'die',
        'do',
        'echo',
        'else',
        'elseif',
        'empty',
        'enddeclare',
        'endfor',
        'endforeach',
        'endif',
        'endswitch',
        'endwhile',
        'eval',
        'exit',
        'extends',
        'final',
        'finally',
        'fn',
        'for',
        'foreach',
        'function',
        'global',
        'goto',
        'if',
        'implements',
        'include',
        'include_once',
        'instanceof',
        'insteadof',
        'interface',
        'isset',
        'list',
        'namespace',
        'new',
        'or',
        'print',
        'private',
        'protected',
        'public',
        'require',
        'require_once',
        'return',
        'static',
        'switch',
        'throw',
        'trait',
        'try',
        'unset',
        'use',
        'var',
        'while',
        'xor',
        'yield',
    ];

    public function __construct(protected Application $app)
    {
        $this->files = $this->app->get(Filesystem::class);

        parent::__construct($app);
    }

    abstract protected function getStub(): string;

    protected function handle(): void
    {
        // First we need to ensure that the given name is not a reserved word within the PHP
        // language and that the class name will actually be valid. If it is not valid we
        // can error now and prevent from polluting the filesystem using invalid files.
        if ($this->isReservedName($this->getNameInput())) {
            $this->error('The name "' . $this->getNameInput() . '" is reserved by PHP.');

            return;
        }

        $name = $this->qualifyClass($this->getNameInput());

        $path = $this->getPath($name);

        // Next, We will check to see if the class already exists. If it does, we don't want
        // to create the class and overwrite the user's code. So, we will bail out so the
        // code is untouched. Otherwise, we will continue generating this class' files.
        if (!$this->option('force') && $this->alreadyExists($this->getNameInput())) {
            $this->error($this->type . ' already exists!');

            return;
        }

        // Next, we will generate the path to the location where this class' file should get
        // written. Then, we will build the class and make the proper replacements on the
        // stub files so that it gets the correctly formatted namespace and class name.
        $this->makeDirectory($path);

        $this->files->put($path, $this->sortImports($this->buildClass($name)));

        $this->success($this->type . ' created successfully.')
            ->newLine()
            ->info('The file can be found here:')
            ->line("<light-grey>{$path}</light-grey>");
    }

    protected function qualifyClass(string $name): string
    {
        $name = Str::ltrim($name, '\\/');

        $name = Str::replace('/', '\\', $name);

        $rootNamespace = $this->rootNamespace();

        if (strpos($name, $rootNamespace) === 0) {
            return $name;
        }

        return $this->qualifyClass(
            $this->getDefaultNamespace(Str::trim($rootNamespace, '\\')) . '\\' . $name
        );
    }

    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace;
    }

    protected function alreadyExists(string $rawName): bool
    {
        return $this->files->exists($this->getPath($this->qualifyClass($rawName)));
    }

    protected function getPath(string $name): string
    {
        $position = strpos($name, $this->rootNamespace());

        if ($position !== false) {
            $name = substr_replace($name, '', $position, strlen($this->rootNamespace()));
        }

        return $this->app->path('app' . DIRECTORY_SEPARATOR . Str::replace('\\', DIRECTORY_SEPARATOR, $name) . '.php');
    }

    protected function makeDirectory(string $path): string
    {
        $directory = dirname($path);

        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0777, true, true);
        }
        return $path;
    }

    protected function buildClass(string $name): string
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    protected function replaceNamespace(string &$stub, string $name): static
    {
        $searches = [
            ['DummyNamespace', 'DummyRootNamespace'],
            ['{{ namespace }}', '{{ rootNamespace }}'],
            ['{{namespace}}', '{{rootNamespace}}'],
        ];

        foreach ($searches as $search) {
            $stub = Str::replace(
                $search,
                [$this->getNamespace($name), $this->rootNamespace()],
                $stub
            );
        }

        return $this;
    }

    protected function getNamespace(string $name): string
    {
        return Str::trim(implode('\\', Arr::slice(explode('\\', $name), 0, -1)), '\\');
    }

    protected function replaceClass(string $stub, string $name): string
    {
        $class = Str::replace($this->getNamespace($name) . '\\', '', $name);

        return Str::replace(['DummyClass', '{{ class }}', '{{class}}'], $class, $stub);
    }

    protected function sortImports(string $stub): string
    {
        if (preg_match('/(?P<imports>(?:use [^;]+;$\n?)+)/m', $stub, $match)) {
            $imports = Arr::sort(Str::explode(Str::trim($match['imports']), "\n"));

            return Str::replace(Str::trim($match['imports']), Arr::join($imports, "\n"), $stub);
        }

        return $stub;
    }

    protected function getNameInput(): string
    {
        return Str::trim($this->argument('name'));
    }

    protected function rootNamespace(): string
    {
        return 'App\\';
    }

    protected function isReservedName(string $name): bool
    {
        $name = Str::lower($name);

        return in_array($name, $this->reservedNames);
    }
}
