<?php

namespace Navigator\Database\Models\Concerns;

trait HasContent
{
    public function content(): string
    {
        return apply_filters('the_content', $this->post_content);
    }

    public function setContent(string $content): bool
    {
        return $this->update(['post_content' => $content]);
    }
}
