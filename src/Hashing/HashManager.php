<?php

namespace Navigator\Hashing;

class HashManager implements HasherInterface
{
    /** @var array<string, HasherInterface> */
    protected array $drivers = [];

    public function add(Hash $driver, HasherInterface $hasher): void
    {
        $this->drivers[$driver->value] = $hasher;
    }

    public function driver(Hash $driver = Hash::BCRYPT): HasherInterface
    {
        return $this->drivers[$driver->value];
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
