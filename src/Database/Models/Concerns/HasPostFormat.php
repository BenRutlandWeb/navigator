<?php

namespace Navigator\Database\Models\Concerns;

use Navigator\WordPress\Concerns\PostFormat;

trait HasPostFormat
{
    /** @param PostFormat $format */
    public function hasPostFormat(PostFormat $format): bool
    {
        return has_post_format($format->value, $this->object);
    }

    public function postFormat(): ?PostFormat
    {
        return PostFormat::tryFrom(get_post_format($this->object));
    }

    public function setPostFormat(?PostFormat $format): bool
    {
        $format = set_post_format($this->object, $format?->value ?? '');

        return $format && !is_wp_error($format);
    }

    public function removePostFormat(): bool
    {
        return $this->setPostFormat(null);
    }
}
