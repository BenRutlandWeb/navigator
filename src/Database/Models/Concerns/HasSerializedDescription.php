<?php

namespace Navigator\Database\Models\Concerns;

trait HasSerializedDescription
{
    public function description(): mixed
    {
        return maybe_unserialize(base64_decode($this->description));
    }

    public function setDescription(mixed $description): bool
    {
        return $this->update(['description' => base64_encode(maybe_serialize($description))]);
    }
}
