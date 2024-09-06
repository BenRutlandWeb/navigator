<?php

namespace Navigator\Database\Query;

class PostAttributes extends Attributes
{
    public function forQuery(): array
    {
        $resolved = [];

        foreach ($this->attributes as $key => $value) {
            $resolved[$this->resolveKey($key)] = $value;
        }

        return $resolved;
    }

    public function resolveKey(string $key): string
    {
        $keys = [
            'post_author' => 'author',
        ];

        return $keys[$key] ?? $key;
    }
}
