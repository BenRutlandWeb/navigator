<?php

namespace Navigator\WordPress;

use Navigator\Foundation\ServiceProvider;

class WordPressServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(WordPressFactory::class, fn() => new WordPressFactory());
    }

    public function boot(): void
    {
        //
    }
}
