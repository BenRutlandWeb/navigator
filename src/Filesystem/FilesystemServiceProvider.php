<?php

namespace Navigator\Filesystem;

use Navigator\Foundation\ServiceProvider;

class FilesystemServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Filesystem::class, function () {
            return new Filesystem();
        });
    }

    public function boot(): void
    {
        //
    }
}
