<?php

namespace Navigator\Database\Models\Concerns;

trait HasExcerpt
{
    public function hasExcerpt(): bool
    {
        return has_excerpt($this->object);
    }

    public function excerpt(): string
    {
        return apply_filters('the_excerpt', get_the_excerpt($this->object));
    }

    public function setExcerpt(string $excerpt): bool
    {
        return $this->update(['post_excerpt' => $excerpt]);
    }
}
