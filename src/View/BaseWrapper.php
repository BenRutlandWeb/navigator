<?php

namespace Navigator\View;

use Navigator\Collections\Arr;
use Navigator\Str\Str;
use Stringable;

class BaseWrapper implements Stringable
{
    protected static $template;

    protected static $base;

    protected string $slug = '';

    protected array $templates = [];

    public function __construct(string $template = 'base.php')
    {
        $this->slug = Str::basename($template, '.php');

        $this->templates = [$template];

        if (static::$base) {
            $str = Str::substr($template, 0, -4);

            Arr::prepend($this->templates, sprintf($str . '-%s.php', self::$base));
        }
    }

    public function template(): void
    {
        require_once static::$template;
    }

    public static function wrap(string $main): static
    {
        static::$template = $main;

        static::$base = Str::basename(static::$template, '.php');

        if (static::$base === 'index') {
            static::$base = false;
        }

        return new static();
    }

    public function __toString(): string
    {
        return locate_template($this->templates);
    }
}
