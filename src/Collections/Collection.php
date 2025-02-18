<?php

namespace Navigator\Collections;

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

    public function avg(): mixed
    {
        return Arr::avg($this->items);
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

    public function containsAll(array $values): bool
    {
        return Arr::hasAll($values, $this->items);
    }

    public function containsAny(array $values): bool
    {
        return Arr::hasAny($values, $this->items);
    }

    public function containsOneItem(): bool
    {
        return $this->count() === 1;
    }

    public function count(): int
    {
        return Arr::count($this->items);
    }

    /** @param (callable(TValue, TKey): mixed) $callback */
    public function each(callable $callback): static
    {
        Arr::each($this->items, $callback);

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
            return Arr::first($this->items) ?? $default;
        }

        foreach ($this->items as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return $default;
    }

    public function flatten(int $depth = 256): static
    {
        return new static(Arr::flatten($this->items, $depth));
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

    public function max(): mixed
    {
        return Arr::max($this->items);
    }

    /** * @param array<TKey, TValue> $items */
    public function merge(array $items): static
    {
        return new static(Arr::merge($this->items, $items));
    }

    public function min(): mixed
    {
        return Arr::min($this->items);
    }

    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
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
        return new static(Arr::pluck($this->items, $value, $key));
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
            $this->items = Arr::merge([$key => $value], $this->items);
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
        return new static(Arr::range($from, $to));
    }

    /**
     * @template TReduceCarry
     * @param (callable(TReduceCarry, TValue): TReduceCarry) $callback
     * @param TReduceCarry $initial
     * @return TReduceCarry
     */
    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        return Arr::reduce($this->items, $callback, $initial);
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

    public function sum(): int|float
    {
        return Arr::sum($this->items);
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

    /**
     * @template TWhenCondition
     * @template TWhenReturn
     * @param TWhenCondition $condition
     * @param (callable(static, TWhenCondition): TWhenReturn) $callback
     * @param (callable(static, TWhenCondition): TWhenReturn)|null $default
     * @return static|TWhenReturn
     */
    public function when(mixed $condition, callable $callback, ?callable $default = null): mixed
    {
        if ($condition) {
            return $callback($this, $condition) ?? $this;
        } elseif ($default) {
            return $default($this, $condition) ?? $this;
        }

        return $this;
    }

    /**
     * @template TWhenEmptyReturn
     * @param (callable(static, bool): TWhenEmptyReturn) $callback
     * @param (callable(static, bool): TWhenEmptyReturn)|null $default
     * @return static|TWhenEmptyReturn
     */
    public function whenEmpty(callable $callback, ?callable $default = null): mixed
    {
        return $this->when($this->isEmpty(), $callback, $default);
    }

    /**
     * @template TWhenNotEmptyReturn
     * @param (callable(static, bool): TWhenNotEmptyReturn) $callback
     * @param (callable(static, bool): TWhenNotEmptyReturn)|null $default
     * @return static|TWhenNotEmptyReturn
     */
    public function whenNotEmpty(callable $callback, ?callable $default = null): mixed
    {
        return $this->when($this->isNotEmpty(), $callback, $default);
    }

    /**
     * @template TUnlessCondition
     * @template TUnlessReturn
     * @param TWhenCondition $condition
     * @param (callable(static, TUnlessCondition): TUnlessReturn) $callback
     * @param (callable(static, TUnlessCondition): TUnlessReturn)|null $default
     * @return static|TUnlessReturn
     */
    public function unless(mixed $condition, callable $callback, ?callable $default = null): mixed
    {
        if (!$condition) {
            return $callback($this, $condition) ?? $this;
        } elseif ($default) {
            return $default($this, $condition) ?? $this;
        }

        return $this;
    }

    /**
     * @template TUnlessEmptyReturn
     * @param (callable(static, bool): TUnlessEmptyReturn) $callback
     * @param (callable(static, bool): TUnlessEmptyReturn)|null $default
     * @return static|TUnlessEmptyReturn
     */
    public function unlessEmpty(callable $callback, ?callable $default = null): mixed
    {
        return $this->unless($this->isEmpty(), $callback, $default);
    }

    /**
     * @template TUnlessNotEmptyReturn
     * @param (callable(static, bool): TUnlessNotEmptyReturn) $callback
     * @param (callable(static, bool): TUnlessNotEmptyReturn)|null $default
     * @return static|TUnlessNotEmptyReturn
     */
    public function unlessNotEmpty(callable $callback, ?callable $default = null): mixed
    {
        return $this->unless($this->isNotEmpty(), $callback, $default);
    }
}
