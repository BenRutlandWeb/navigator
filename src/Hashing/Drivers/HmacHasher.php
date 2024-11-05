<?php

namespace Navigator\Hashing\Drivers;

use Navigator\Hashing\HasherInterface;

class HmacHasher implements HasherInterface
{
    public function __construct(protected string $key)
    {
        //
    }

    public function info(string $hashedValue): array
    {
        return [];
    }

    public function make(string $value, array $options = []): string
    {
        return hash_hmac($options['algo'] ?? 'sha256', $value, $this->key, $options['binary'] ?? false);
    }

    public function check(string $value, string $hashedValue, array $options = []): bool
    {
        if (strlen($hashedValue) === 0) {
            return false;
        }

        return hash_equals($this->make($value, $options), $hashedValue);
    }

    public function needsRehash(string $hashedValue, array $options = []): bool
    {
        return false;
    }
}
