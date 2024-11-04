<?php

namespace Navigator\Hashing;

use InvalidArgumentException;

class HashManager implements HasherInterface
{
    public function __construct(protected array $drivers)
    {
        //
    }

    public function driver(?string $driver = 'bcrypt'): HasherInterface
    {
        return $this->drivers[$driver] ?? throw new InvalidArgumentException("Driver [$driver] not supported.");
    }

    public function info(string $hashedValue): array
    {
        return $this->driver()->info($hashedValue);
    }

    public function make(string $value, array $options = []): string
    {
        return $this->driver()->make($value, $options);
    }

    public function check(string $value, string $hashedValue, array $options = []): bool
    {
        return $this->driver()->check($value, $hashedValue, $options);
    }

    public function needsRehash(string $hashedValue, array $options = []): bool
    {
        return $this->driver()->needsRehash($hashedValue, $options);
    }
}
