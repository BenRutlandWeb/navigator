<?php

namespace Navigator\Hashing;

use Navigator\Foundation\ServiceProvider;

class HashServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Hasher::class, fn () => new Hasher);
    }

    public function boot(): void
    {
        //
    }
}
