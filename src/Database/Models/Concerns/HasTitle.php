<?php

namespace Navigator\Database\Models\Concerns;

trait HasTitle
{
    public function title(): string
    {
        return get_the_title($this->object);
    }

    public function setTitle(string $title): bool
    {
        return $this->update(['post_title' => $title]);
    }
}
