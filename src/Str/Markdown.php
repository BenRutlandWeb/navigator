<?php

namespace Navigator\Str;

use Navigator\Foundation\Concerns\Htmlable;
use Parsedown;
use Stringable;

class Markdown extends Parsedown implements Stringable, Htmlable
{
    public function __construct(public readonly string $content)
    {
        //
    }

    public static function from(string $string): static
    {
        return new static($string);
    }

    public function toHtml(): string
    {
        return $this->setUrlsLinked(false)->text($this->content);
    }

    public function __toString(): string
    {
        return $this->toHtml();
    }
}
