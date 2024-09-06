<?php

namespace Navigator\Config;

use Navigator\Str\Str;

class Repository
{
    public function __construct(protected array $config = [])
    {
        //
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $parts = Str::explode($key, '.');

        $config = $this->config;

        foreach ($parts as $k) {
            if (isset($config[$k])) {
                $config = $config[$k];
            } else {
                return $default;
            }
        }

        return $config ?? $default;
    }

    public function has(string $key): bool
    {
        return $this->get($key) ? true : false;
    }

    public function set(string|array $key, mixed $value = null): mixed
    {
        if (!$value && is_array($key)) {
            return $this->config = $key;
        }

        $parts = Str::explode($key, '.');

        $config = $this->config;

        while (count($parts) > 1) {
            $key = array_shift($parts);

            if (!isset($config[$key]) || !is_array($config[$key])) {
                $config[$key] = [];
            }

            $config = &$config[$key];
        }

        $config[array_shift($parts)] = $value;

        return $this->config = $config;
    }
}
