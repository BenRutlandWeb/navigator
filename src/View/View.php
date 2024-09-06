<?php

namespace Navigator\View;

use Navigator\Foundation\Concerns\Htmlable;

class View implements Htmlable
{
    public function __construct(protected string $path, protected array $data = [])
    {
        //
    }

    public function get(): string
    {
        return $this->evaluatePath($this->path, $this->data);
    }

    public function evaluatePath(string $__path, array $__data = []): string
    {
        ob_start();

        (static function () use ($__path, $__data) {
            extract($__data, EXTR_SKIP);

            return require $__path;
        })();

        return ltrim(ob_get_clean());
    }

    public function toHtml(): string
    {
        return $this->get();
    }

    public function __toString(): string
    {
        return $this->get();
    }
}
