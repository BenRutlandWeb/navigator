<?php

namespace Navigator\Database\Models\Concerns;

use Navigator\Database\BuilderInterface;

trait HasChildren
{
    /** @return ?static  */
    public function parent()
    {
        return $this->belongsTo(static::class);
    }

    /** @return BuilderInterface<static> */
    public function children()
    {
        return $this->hasMany(static::class);
    }
}
