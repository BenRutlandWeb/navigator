<?php

namespace Navigator\Collections;

use Throwable;

class Arr
{
    public static function append(array $items, mixed ...$item): array
    {
        array_push($items, ...$item);

        return $items;
    }

    public static function chunk(array $items, int $length, bool $preserveKeys = false): array
    {
        return array_chunk($items, $length, $preserveKeys);
    }

    public static function collect(array $items): Collection
    {
        return Collection::make($items);
    }

    public static function combine(array $keys, array $values): array|bool
    {
        return array_combine($keys, $values);
    }

    public static function count(array $items): int
    {
        return count($items);
    }

    public static function diff(mixed $items, array ...$arrays): array
    {
        return array_diff($items, ...$arrays);
    }

    public static function fill(mixed $item, int $count, int $start = 0): array
    {
        return array_fill($start, $count, $item);
    }

    public static function filter(array $items, ?callable $callback = null): array
    {
        return array_filter($items, $callback, ARRAY_FILTER_USE_BOTH);
    }

    public static function first(array $items): mixed
    {
        return array_shift($items);
    }

    public static function firstKey(array $items): string|int|null
    {
        return array_key_first($items);
    }

    public static function flip(array $items): array
    {
        return array_flip($items);
    }

    public static function hasKey(string|int $needle, array $haystack): bool
    {
        return array_key_exists($needle, $haystack);
    }

    public static function has(mixed $needle, array $haystack, bool $strict = false): bool
    {
        return in_array($needle, $haystack, $strict);
    }

    public static function implode(array $items, array|string $separator = ""): string
    {
        return static::join($items, $separator);
    }

    public static function intersect(array $items, array ...$arrays): array
    {
        return array_intersect($items, ...$arrays);
    }

    public static function isList(array $items): bool
    {
        return array_is_list($items);
    }

    public static function join(array $items, array|string $separator = ""): string
    {
        return join($separator, $items);
    }

    public static function keys(array $items): array
    {
        return array_keys($items);
    }

    public static function last(array $items): mixed
    {
        return array_pop($items);
    }

    public static function lastKey(array $items): string|int|null
    {
        return array_key_last($items);
    }

    public static function length(array $items): int
    {
        return static::count($items);
    }

    public static function map(array $items, callable $callback): array
    {
        $keys = static::keys($items);

        try {
            $items = array_map($callback, $items, $keys);
        } catch (Throwable) {
            $items = array_map($callback, $items);
        }

        return static::combine($keys, $items);
    }

    public static function mapWithKeys(array $items, callable $callback): array
    {
        $result = [];

        foreach ($items as $key => $value) {
            $assoc = $callback($value, $key);

            foreach ($assoc as $mapKey => $mapValue) {
                $result[$mapKey] = $mapValue;
            }
        }

        return $result;
    }

    public static function merge(array ...$items): array
    {
        return array_merge(...$items);
    }

    public static function mergeRecursive(array ...$items): array
    {
        return array_merge_recursive(...$items);
    }

    public static function pad(array $items, int $length, mixed $value = null): array
    {
        return array_pad($items, $length, $value);
    }

    public static function pluck(array $items, string|int $columnKey, string|int|null $indexKey = null): array
    {
        return wp_list_pluck($items, $columnKey, $indexKey);
    }

    public static function prepend(array $items, mixed ...$item): array
    {
        array_unshift($items, ...$item);

        return $items;
    }

    public static function product(array $items): int|float
    {
        return array_product($items);
    }

    public static function random(array $items, int $num = 1): array
    {
        $keys = array_rand($items, $num);

        return static::map((array) $keys, fn ($key) => $items[$key]);
    }

    public static function query(array $items): string
    {
        return http_build_query($items, '', '&', PHP_QUERY_RFC3986);
    }

    public static function range(string|int|float $start, string|int|float $end, int|float $step = 1): array
    {
        return range($start, $end, $step);
    }

    public static function replace(array $items, array ...$replacements): ?array
    {
        return array_replace($items, ...$replacements);
    }

    public static function reverse(array $items, bool $preserveKeys = false): array
    {
        return array_reverse($items, $preserveKeys);
    }

    public static function shuffle(array $items): array
    {
        shuffle($items);

        return $items;
    }

    public static function slice(array $items, int $offset, ?int $length, bool $preserveKeys = false): array
    {
        return array_slice($items, $offset, $length, $preserveKeys);
    }

    public static function sort(array $items, ?callable $callback = null, int $flags = SORT_REGULAR): array
    {
        if ($callback) {
            usort($items, $callback);
        } else {
            sort($items, $flags);
        }

        return $items;
    }

    public static function sortDesc(array $items, int $flags = SORT_REGULAR): array
    {
        rsort($items, $flags);

        return $items;
    }

    public static function sortKeys(array $items, ?callable $callback = null, int $flags = SORT_REGULAR): array
    {
        if ($callback) {
            uksort($items, $callback);
        } else {
            ksort($items, $flags);
        }

        return $items;
    }

    public static function sortKeysDesc(array $items, int $flags = SORT_REGULAR): array
    {
        krsort($items, $flags);

        return $items;
    }

    public static function sum(array $items): int|float
    {
        return array_sum($items);
    }

    public static function toCssClasses(array $items): string
    {
        $classes = [];

        foreach ($items as $class => $constraint) {
            if (is_numeric($class)) {
                $classes[] = $constraint;
            } elseif ($constraint) {
                $classes[] = $class;
            }
        }

        return static::join($classes, ' ');
    }

    public static function toCssStyles(array $items): string
    {
        return static::join(static::map($items, fn ($v, $p) => "{$p}: {$v};"), ' ');
    }

    public static function toHtmlAttributes(array $items): string
    {
        return static::join(static::map($items, function ($v, $p) {
            if ($v && is_scalar($v)) {
                return "{$p}=\"{$v}\"";
            }
        }), ' ');
    }

    public static function unique(array $items, int $flags = SORT_STRING): array
    {
        return array_unique($items, $flags);
    }

    public static function values(array $items): array
    {
        return array_values($items);
    }
}
