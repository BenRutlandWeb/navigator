<?php

namespace Navigator\WordPress;

use Navigator\Foundation\ServiceProvider;
use Navigator\WordPress\Console\Commands\MediaLibraryClean;
use Navigator\WordPress\Console\Commands\MediaLibraryRegenerate;

class WordPressServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(WordPressFactory::class, fn() => new WordPressFactory());
    }

    public function boot(): void
    {
        $this->commands([
            MediaLibraryClean::class,
            MediaLibraryRegenerate::class,
        ]);
    }
}
