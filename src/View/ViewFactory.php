<?php

namespace Navigator\View;

use Navigator\Filesystem\Filesystem;
use Navigator\Str\Markdown;
use Navigator\Str\Str;
use Navigator\Str\Stringable;

class ViewFactory
{
    public function __construct(protected Filesystem $files, protected string $dir)
    {
        //
    }

    public function make(string $path, array $data = []): View
    {
        return new View($this->normalizeName($path), $data);
    }

    public function markdown(string $path, array $data = []): Markdown
    {
        return new Markdown($this->make($path, $data));
    }

    protected function normalizeName(string $path): Stringable
    {
        return Str::of($path)->replace(['/', '.'], DIRECTORY_SEPARATOR)
            ->trim('/' . DIRECTORY_SEPARATOR)
            ->wrap($this->dir . DIRECTORY_SEPARATOR, '.php');
    }

    public function exists(string $path): bool
    {
        return $this->files->exists($this->normalizeName($path));
    }
}
