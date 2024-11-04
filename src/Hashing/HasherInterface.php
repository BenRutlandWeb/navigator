<?php

namespace Navigator\Hashing;

interface HasherInterface
{
    public function info(string $hashedValue): array;

    public function make(string $value, array $options = []): string;

    public function check(string $value, string $hashedValue, array $options = []): bool;

    public function needsRehash(string $hashedValue, array $options = []): bool;
}
