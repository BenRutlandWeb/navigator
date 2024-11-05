<?php

namespace Navigator\Hashing;

use Navigator\Foundation\Application;
use Navigator\Foundation\ServiceProvider;

class HashServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(BcryptHasher::class, fn() => new BcryptHasher());

        $this->app->singleton(HmacHasher::class, function (Application $app) {
            return new HmacHasher($app->env('AUTH_KEY'));
        });

        $this->app->singleton(HashManager::class, function (Application $app) {
            $manager = new HashManager();

            $manager->add(Hash::BCRYPT, $app->get(BcryptHasher::class));
            $manager->add(Hash::HMAC, $app->get(HmacHasher::class));

            return $manager;
        });
    }

    public function boot(): void
    {
        //
    }
}
