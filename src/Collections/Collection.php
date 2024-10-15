<?php

namespace Navigator\Collections;

use ArgumentCountError;
use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Navigator\Foundation\Concerns\Arrayable;
use Traversable;

/**
 * @template TKey of array-key
 * @template-covariant TValue
 */
class Collection implements Arrayable, ArrayAccess, Countable, IteratorAggregate, JsonSerializable
{
    /** @param array<TKey, TValue> $items */
    public function __construct(protected array $items = [])
    {
        //
    }

    /** @param TValue $item */
    public function add(mixed $item): static
    {
        $this->items[] = $item;

        return $this;
    }

    /** @return array<TKey, TValue> */
    public function all(): array
    {
        return $this->items;
    }

    /** @return static<int, static> */
    public function chunk(int $size): static
    {
        if ($size <= 0) {
            return new static;
        }

        $chunks = [];

        foreach (Arr::chunk($this->items, $size, true) as $chunk) {
            $chunks[] = new static($chunk);
        }

        return new static($chunks);
    }

    /**
     * @template TCombineValue
     * @param array<array-key, TCombineValue> $values
     * @return static<TValue, TCombineValue>
     */
    public function combine(array $values): static
    {
        return new static(Arr::combine($this->all(), $values));
    }

    public function contains(mixed $key): bool
    {
        return Arr::has($key, $this->items);
    }

    public function containsOneItem(): bool
    {
        return $this->count() === 1;
    }

    public function count(): int
    {
        return count($this->items);
    }

    /** @param (callable(TValue, TKey): mixed) $callback */
    public function each(callable $callback): static
    {
        foreach ($this->items as $key => $item) {
            if ($callback($item, $key) === false) {
                break;
            }
        }

        return $this;
    }

    /** @param (callable(TValue, TKey): bool)|null $callback */
    public function filter(?callable $callback = null): static
    {
        return new static(Arr::filter($this->items, $callback));
    }

    /**
     * @template TFirstDefault
     * @param (callable(TValue, TKey): bool)|null $callback
     * @param TFirstDefault $default
     * @return TValue|TFirstDefault
     */
    public function first(?callable $callback = null, mixed $default = null): mixed
    {
        if (is_null($callback)) {
            if ($this->isEmpty()) {
                return $default;
            }

            foreach ($this->items as $item) {
                return $item;
            }

            return $default;
        }

        foreach ($this->items as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return $default;
    }

    /** @return static<TValue, TKey> */
    public function flip(): static
    {
        return new static(Arr::flip($this->items));
    }

    /**
     * @template TGetDefault
     * @param TKey $key
     * @param TGetDefault $default
     * @return TValue|TGetDefault
     */
    public function get(int|string $key, mixed $default = null): mixed
    {
        return $this->items[$key] ?? $default;
    }

    /** @return ArrayIterator<TKey, TValue> */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function groupBy(string $key): static
    {
        $groups = [];

        foreach ($this->items as $k => $v) {
            $groups[$v[$key]][$k] = $v;
        }

        foreach ($groups as $key => $group) {
            $groups[$key] = new static($group);
        }

        return new static($groups);
    }

    /** @param array-key $key */
    public function has(int|string $key): bool
    {
        return Arr::hasKey($key, $this->items);
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function join(array|string $separator = ''): string
    {
        return Arr::join($this->items, $separator);
    }

    public function implode(array|string $separator = ''): string
    {
        return $this->join($separator);
    }

    /** @return array<TKey, mixed> */
    public function jsonSerialize(): array
    {
        return Arr::map($this->all(), function ($value) {
            if ($value instanceof JsonSerializable) {
                return $value->jsonSerialize();
            } elseif ($value instanceof Arrayable) {
                return $value->toArray();
            }

            return $value;
        });
    }

    /** @return static<int, TKey> */
    public function keys(): static
    {
        return new static(Arr::keys($this->items));
    }

    /**
     * @template TLastDefault
     * @param (callable(TValue, TKey): bool)|null $callback
     * @param TLastDefault $default
     * @return TValue|TLastDefault
     */
    public function last(?callable $callback = null, mixed $default = null): mixed
    {
        return $this->reverse()->first($callback, $default);
    }

    /**
     * @template TMakeKey of array-key
     * @template TMakeValue
     * @param  array<TMakeKey, TMakeValue> $items
     * @return static<TMakeKey, TMakeValue>
     */
    public static function make(array $items = []): static
    {
        return new static($items);
    }

    /**
     * @template TMapValue
     * @param callable(TValue, TKey): TMapValue $callback
     * @return static<TKey, TMapValue>
     */
    public function map(callable $callback): static
    {
        return new static(Arr::map($this->items, $callback));
    }

    /**
     * @template TMapValue
     * @param callable(TValue, TKey): TMapValue $callback
     * @return static<TKey, TMapValue>
     */
    public function mapWithKeys(callable $callback): static
    {
        return new static(Arr::mapWithKeys($this->items, $callback));
    }

    /**
     * @template TMapIntoClass
     * @param class-string<TMapIntoClass> $class
     * @return static<int, TMapIntoClass>
     */
    public function mapInto(string $class): static
    {
        return $this->map(fn($item) => new $class($item));
    }

    /** * @param array<TKey, TValue> $items */
    public function merge(array $items): static
    {
        return new static(Arr::merge($this->items, $items));
    }

    /** @param TKey $key */
    public function offsetExists($key): bool
    {
        return isset($this->items[$key]);
    }

    /**
     * @param TKey $key
     * @return TValue
     */
    public function offsetGet($key): mixed
    {
        return $this->items[$key];
    }

    /**
     * @param ?TKey $key
     * @param TValue $value
     */
    public function offsetSet($key, mixed $value): void
    {
        if (is_null($key)) {
            $this->items[] = $value;
        } else {
            $this->items[$key] = $value;
        }
    }

    /** @param TKey $key */
    public function offsetUnset($key): void
    {
        unset($this->items[$key]);
    }

    /**
     * @param array-key $value
     * @param ?array-key $key
     * @return static<array-key, mixed>
     */
    public function pluck(string $value, ?string $key = null): static
    {
        return new static(wp_list_pluck($this->items, $value, $key));
    }

    /**
     * @param TValue $value
     * @param ?TKey $key
     */
    public function prepend(mixed $value, int|string|null $key = null): static
    {
        if (is_null($key)) {
            $this->items = Arr::prepend($this->items, $value);
        } else {
            $this->items = [$key => $value] + $this->items;
        }

        return $this;
    }

    /** @param TValue ...$values */
    public function push(...$values): static
    {
        foreach ($values as $value) {
            $this->items[] = $value;
        }

        return $this;
    }

    /** @return static<int, int> */
    public static function range(int $from, int $to): static
    {
        return new static(range($from, $to));
    }

    public function reverse(): static
    {
        return new static(Arr::reverse($this->items, true));
    }

    /** @param (callable(TValue, TValue): int)|null|int $callback */
    public function sort(?callable $callback = null): static
    {
        $items = $this->items;

        $callback && is_callable($callback)
            ? uasort($items, $callback)
            : asort($items, $callback ?? SORT_REGULAR);

        return new static($items);
    }

    public function sortKeys(int $options = SORT_REGULAR, bool $descending = false): static
    {
        $items = $this->items;

        $descending ? krsort($items, $options) : ksort($items, $options);

        return new static($items);
    }

    /** @return array<TKey, mixed> */
    public function toArray(): array
    {
        return $this->map(
            fn($value) => $value instanceof Arrayable ? $value->toArray() : $value
        )->all();
    }

    /** @return static<int, TValue> */
    public function values(): static
    {
        return new static(Arr::values($this->items));
    }
}
