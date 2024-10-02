<?php

namespace Navigator\Database;

use ArrayAccess;
use Generator;
use JsonSerializable;
use Navigator\Collections\Collection;
use Navigator\Foundation\Concerns\Arrayable;
use Navigator\Pagination\Paginator;

/** @template T of BuilderInterface */
interface ModelInterface extends Arrayable, ArrayAccess, JsonSerializable
{
    /** @return T<static> */
    public static function query(): BuilderInterface;

    public static function find(int $id): ?static;

    public static function findOrFail(int $id): ?static;

    /** @return Collection<int, static> */
    public static function all(): Collection;

    /** @param (callable(Collection<int, static>, int): mixed) $callback */
    public static function chunk(int $count, callable $callback): bool;

    /** @return Generator<static> */
    public static function lazy(int $chunk = 1000): Generator;

    /** @return Paginator<static> */
    public static function paginate(int $perPage = 15, string $pageName = 'page', ?int $page = null, ?int $total = null): Paginator;

    public function id(): int;

    public static function create(array $attributes): ?static;

    public function update(array $attributes): bool;

    public function fill(array $attributes): static;

    public function save(): bool;

    public function delete(): bool;

    /** @param int|array<int, int> $ids */
    public static function destroy(int|array $ids): int;
}
