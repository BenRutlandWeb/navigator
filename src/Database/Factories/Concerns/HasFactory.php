<?php

namespace Navigator\Database\Factories\Concerns;

use Navigator\Database\Factories\Factory;

trait HasFactory
{
    public static function factory(): Factory
    {
        return static::newFactory() ?? Factory::forModel(static::class);
    }

    public static function newFactory(): ?Factory
    {
        return null;
    }
}
