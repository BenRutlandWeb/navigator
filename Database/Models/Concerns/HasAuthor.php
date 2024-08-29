<?php

namespace Navigator\Database\Models\Concerns;

use Navigator\Database\Models\User;

trait HasAuthor
{
    abstract public function author();

    public function setAuthor(User $user): bool
    {
        return $this->associate($user->id());
    }
}
