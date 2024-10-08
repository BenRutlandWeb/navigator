<?php

namespace Navigator\Encryption;

use Navigator\Encryption\Console\Commands\KeyGenerate;
use Navigator\Encryption\Exceptions\MissingAppKeyException;
use Navigator\Foundation\Application;
use Navigator\Foundation\ServiceProvider;
use Navigator\Str\Str;

class EncryptionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Encrypter::class, function (Application $app) {
            return new Encrypter(
                $this->parseKey($app->env('APP_KEY')),
                $app->env('APP_CIPHER', 'aes-256-cbc')
            );
        });
    }

    public function boot(): void
    {
        $this->commands([
            KeyGenerate::class,
        ]);
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
