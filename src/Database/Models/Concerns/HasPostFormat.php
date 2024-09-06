<?php

namespace Navigator\Database\Models\Concerns;

trait HasPostFormat
{
    /** @param string|array<int, string> $format */
    public function hasPostFormat(string|array $format): bool
    {
        return has_post_format($format, $this->object);
    }

    public function postFormat(): ?string
    {
        return get_post_format($this->object) ?: null;
    }

    public function setPostFormat(string $format): bool
    {
        $format = set_post_format($this->object, $format);

        return $format && !is_wp_error($format);
    }

    public function removePostFormat(): bool
    {
        $format = set_post_format($this->object, '');

        return $format && !is_wp_error($format);
    }
}
