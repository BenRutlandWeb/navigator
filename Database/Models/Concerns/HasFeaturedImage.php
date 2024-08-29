<?php

namespace Navigator\Database\Models\Concerns;

trait HasFeaturedImage
{
    public function hasFeaturedImage(): bool
    {
        return has_post_thumbnail($this->object);
    }

    public function featuredImageId(): int
    {
        return get_post_thumbnail_id($this->object);
    }

    /**
     * @param string|array<int, int> $size
     * @param string|array $attr
     */
    public function featuredImage(string|array $size = 'post-thumbnail', string|array $attr = ''): string
    {
        return get_the_post_thumbnail($this->object, $size, $attr);
    }

    /** @param string|array<int, int> $size */
    public function featuredImageUrl(string|array $size = 'post-thumbnail'): string
    {
        return get_the_post_thumbnail_url($this->object, $size);
    }

    public function featuredImageCaption(): string
    {
        return get_the_post_thumbnail_caption($this->object);
    }

    public function setFeaturedImage(int $id): bool
    {
        return set_post_thumbnail($this->object, $id);
    }
}
