<?php

namespace Navigator\Hashing;

use Navigator\Encryption\Exceptions\MissingAppKeyException;
use Navigator\Foundation\Application;
use Navigator\Foundation\ServiceProvider;
use Navigator\Str\Str;

class HashServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Hasher::class, fn(Application $app) => new Hasher(
            $this->parseKey($app->env('APP_KEY'))
        ));
    }

    public function boot(): void
    {
        //
    }

    protected function parseKey(string $key): string
    {
        if (empty($key)) {
            throw new MissingAppKeyException();
        }

        $key = Str::of($key);

        if ($key->startsWith($prefix = 'base64:')) {
            $key = $key->replace($prefix, '')->fromBase64();
        }

        return $key;
    }
}
