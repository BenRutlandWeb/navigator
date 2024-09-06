<?php

namespace Navigator\Cache;

use ArrayAccess;
use Navigator\Collections\Arr;
use Navigator\Database\Connection;

class Repository implements ArrayAccess
{
    protected int $default = 3600;

    public function __construct(protected Connection $connection, public readonly string $prefix = '')
    {
        //
    }

    public function flush(): bool
    {
        $flush = $this->connection->query(
            "DELETE FROM {$this->connection->options} WHERE option_name LIKE ('%\_transient\_%')"
        );

        return $flush !== false;
    }

    public function clear(): bool
    {
        $flush = $this->connection->query(
            "DELETE FROM {$this->connection->options} WHERE option_name LIKE ('%\_transient\_{$this->prefix}%')"
        );

        return $flush !== false;
    }

    public function forever(string $key, mixed $value): bool
    {
        return $this->put($key, $value,  0);
    }

    public function forget(string $key): bool
    {
        return $this->delete($key);
    }

    public function delete(string $key): bool
    {
        return delete_transient($this->prefix . $key);
    }

    public function add(string $key, mixed $value, int $ttl = 0): bool
    {
        if (!$this->has($key)) {
            return $this->put($key, $value,  $ttl);
        }

        return false;
    }

    public function put(string $key, mixed $value, int $ttl = 0): bool
    {
        return $this->set($key, $value, $ttl);
    }

    public function set(string $key, mixed $value, int $ttl = 0): bool
    {
        if ($ttl < 0) {
            return $this->forget($key);
        }

        return set_transient($this->prefix . $key, $value, $ttl);
    }

    public function increment(string $key, int $value = 1): int|bool
    {
        $current = $this->get($key, 0);

        if (!is_numeric($current)) {
            return false;
        }

        $this->put($key, $new = $current + $value);

        return $new;
    }

    public function decrement(string $key, int $value = 1): int|bool
    {
        $current = $this->get($key, 0);

        if (!is_numeric($current)) {
            return false;
        }

        $this->put($key, $new = $current - $value);

        return $new;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return get_transient($this->prefix . $key) ?: $default;
    }

    public function pull(string $key, mixed $default = null): mixed
    {
        $value = $this->get($key, $default);

        $this->forget($key);

        return $value;
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== false;
    }

    public function missing(string $key): bool
    {
        return !$this->has($key);
    }

    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        $value = $this->get($key);

        if (!is_null($value)) {
            return $value;
        }

        $this->put($key, $value = $callback(), $ttl);

        return $value;
    }

    public function rememberForever(string $key, callable $callback): mixed
    {
        $value = $this->get($key);

        if (!is_null($value)) {
            return $value;
        }

        $this->forever($key, $value = $callback());

        return $value;
    }

    /**
     * @template TManyDefault
     * @param array<int, string>|array<string, TManyDefault> $keys
     * @return array<string, ?TManyDefault>
     */
    public function many(array $keys): array
    {
        return Arr::mapWithKeys($keys, function ($value, $key) {
            $k = is_string($key) ? $key : $value;

            return [$k => $this->get($k, is_string($key) ? $value : null)];
        });
    }

    /** @param array<string, mixed> $values */
    public function putMany(array $values, int $ttl = 0): bool
    {
        $manyResult = null;

        foreach ($values as $key => $value) {
            $result = $this->put($key, $value, $ttl);

            $manyResult = is_null($manyResult) ? $result : $result && $manyResult;
        }

        return $manyResult ?: false;
    }

    /** @param array<int, string> $keys */
    public function deleteMany(array $keys): bool
    {
        $result = true;

        foreach ($keys as $key) {
            if (!$this->delete($key)) {
                $result = false;
            }
        }

        return $result;
    }

    /** @param string $key */
    public function offsetExists($key): bool
    {
        return $this->has($key);
    }

    /** @param string $key */
    public function offsetGet($key): mixed
    {
        return $this->get($key);
    }

    /** @param string $key */
    public function offsetSet($key, mixed $value): void
    {
        $this->set($key, $value, $this->default);
    }

    /** @param string $key */
    public function offsetUnset($key): void
    {
        $this->delete($key);
    }
}
