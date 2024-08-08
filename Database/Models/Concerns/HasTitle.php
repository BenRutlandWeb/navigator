<?php

namespace Navigator\Database\Models\Concerns;

trait HasTitle
{
    public function title(): string
    {
        return get_the_title($this->object);
    }
}
