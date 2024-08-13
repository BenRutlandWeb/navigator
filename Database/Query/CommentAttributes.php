<?php

namespace Navigator\Database\Query;

class CommentAttributes extends Attributes
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
            'comment_parent'  => 'parent',
            'comment_post_ID' => 'post_id',
        ];

        return $keys[$key] ?? $key;
    }
}
