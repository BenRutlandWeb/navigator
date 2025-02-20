<?php

namespace Navigator\Session;

use ArrayAccess;

class Session implements ArrayAccess
{
    public function all(): array
    {
        return $_SESSION;
    }

    public function forget(string $key): bool
    {
        return $this->delete($key);
    }

    public function delete(string $key): bool
    {
        unset($_SESSION[$key]);

        return true;
    }

    public function add(string $key, mixed $value): bool
    {
        if (!$this->has($key)) {
            return $this->put($key, $value);
        }

        return false;
    }

    public function put(string $key, mixed $value): bool
    {
        return $this->set($key, $value);
    }

    public function set(string $key, mixed $value): bool
    {
        $_SESSION[$key] = $value;

        return true;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function pull(string $key, mixed $default = null): mixed
    {
        $value = $this->get($key, $default);

        $this->forget($key);

        return $value;
    }

    public function increment(string $key, int $amount = 1): int
    {
        $this->put($key, $value = $this->get($key, 0) + $amount);

        return $value;
    }

    public function decrement(string $key, int $amount = 1): int
    {
        return $this->increment($key, $amount * -1);
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
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
        $this->set($key, $value);
    }

    /** @param string $key */
    public function offsetUnset($key): void
    {
        $this->delete($key);
    }
}
