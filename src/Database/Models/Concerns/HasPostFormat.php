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
        $format = get_post_format($this->object) ?: null;

        return $format ? PostFormat::tryFrom($format) : null;
    }

    public function setPostFormat(PostFormat $format): bool
    {
        $format = set_post_format($this->object, $format->value);

        return $format && !is_wp_error($format);
    }

    public function removePostFormat(): bool
    {
        $format = set_post_format($this->object, '');

        return $format && !is_wp_error($format);
    }
}
