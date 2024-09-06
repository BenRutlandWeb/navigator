<?php

namespace Navigator\Database\Query;

class TermAttributes extends Attributes
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
            'parent' => 'child_of',
        ];

        return $keys[$key] ?? $key;
    }
}
