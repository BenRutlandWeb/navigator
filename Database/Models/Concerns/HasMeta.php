<?php

namespace Navigator\Database\Models\Concerns;

use Navigator\Database\Models\Meta;

trait HasMeta
{
    protected static ?Meta $meta = null;

    public function meta(): Meta
    {
        if (!static::$meta) {
            static::$meta = new Meta($this);
        }

        return static::$meta;
    }
}
