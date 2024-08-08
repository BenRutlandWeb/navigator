<?php

namespace Navigator\View;

use Navigator\Str\Markdown;
use Navigator\Str\Str;
use Navigator\Str\Stringable;

class ViewFactory
{
    protected string $view;

    public function __construct(protected string $dir)
    {
        //
    }

    public function make(string $path, array $data = []): View
    {
        $path = $this->normalizeName($path);

        return new View($path->wrap($this->dir . DIRECTORY_SEPARATOR, '.php'), $data);
    }

    public function markdown(string $path, array $data = []): Markdown
    {
        return new Markdown($this->make($path, $data));
    }

    protected function normalizeName(string $path): Stringable
    {
        return Str::of($path)->replace(['/', '.'], DIRECTORY_SEPARATOR)
            ->trim('/' . DIRECTORY_SEPARATOR);
    }
}
