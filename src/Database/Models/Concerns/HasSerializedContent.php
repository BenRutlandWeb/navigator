<?php

namespace Navigator\Database\Models\Concerns;

trait HasSerializedContent
{
    public function content(): mixed
    {
        return maybe_unserialize($this->post_content);
    }

    public function setContent(mixed $content): bool
    {
        return $this->update(['post_content' => maybe_serialize($content)]);
    }
}
