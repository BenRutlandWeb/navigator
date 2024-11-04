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
        $this->app->singleton(BcryptHasher::class, fn() => new BcryptHasher());

        $this->app->singleton(HmacHasher::class, function (Application $app) {
            return new HmacHasher($this->parseKey($app->env('APP_KEY')));
        });

        $this->app->singleton(HashManager::class, fn(Application $app) => new HashManager([
            'bcrypt' => $app->get(BcryptHasher::class),
            'hmac'   => $app->get(HmacHasher::class),
        ]));
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
