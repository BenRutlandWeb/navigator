<?php

namespace Navigator\Database\Models\Concerns;

trait HasSerializedContent
{
    public function content(): mixed
    {
        return maybe_unserialize(base64_decode($this->post_content));
    }

    public function setContent(mixed $content): bool
    {
        return $this->update(['post_content' => base64_encode(maybe_serialize($content))]);
    }
}
