<?php

namespace Navigator\Database;

use ArrayAccess;
use JsonSerializable;
use Navigator\Collections\Collection;
use Navigator\Foundation\Concerns\Arrayable;

/** @template T of BuilderInterface */
interface ModelInterface extends Arrayable, ArrayAccess, JsonSerializable
{
    /** @return T<static> */
    public static function query(): BuilderInterface;

    public static function find(int $id): ?static;

    /** @return Collection<int, static> */
    public static function all(): Collection;

    public function id(): int;

    public static function create(array $attributes = []): ?static;

    public function update(array $attributes = []): bool;

    public function save(): bool;

    public function delete(): bool;
}
