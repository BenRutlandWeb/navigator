<?php

namespace Navigator\Hashing;

class Hasher
{
    public function info(string $hashedValue): array
    {
        return password_get_info($hashedValue);
    }

    public function make(string $value, array $options = []): string
    {
        return password_hash($value, PASSWORD_BCRYPT, $options);
    }

    public function check(string $value, string $hashedValue): bool
    {
        if (strlen($hashedValue) === 0) {
            return false;
        }

        return password_verify($value, $hashedValue);
    }

    public function needsRehash(string $hashedValue, array $options = []): bool
    {
        return password_needs_rehash($hashedValue, PASSWORD_BCRYPT, $options);
    }
}
