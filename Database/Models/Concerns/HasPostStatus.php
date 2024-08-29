<?php

namespace Navigator\Database\Models\Concerns;

use Navigator\Collections\Arr;

trait HasPostStatus
{
    /** @param string|array<int, string> $status */
    public function hasPostStatus(string|array $status): bool
    {
        return Arr::has($this->postStatus(), (array) $status);
    }

    public function postStatus(): string
    {
        return get_post_status($this->object);
    }

    public function setPostStatus(string $status): bool
    {
        return $this->update(['post_status' => $status]);
    }

    public function publish(): static
    {
        $this->setPostStatus('publish');

        return $this;
    }

    public function trash(): static
    {
        $this->setPostStatus('trash');

        return $this;
    }
}
