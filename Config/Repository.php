<?php

namespace Navigator\Config;

use Navigator\Str\Str;

class Repository
{
    public function __construct(protected array $config)
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
}
