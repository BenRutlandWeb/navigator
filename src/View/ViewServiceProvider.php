<?php

namespace Navigator\View;

use Navigator\Filesystem\Filesystem;
use Navigator\Foundation\Application;
use Navigator\Foundation\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ViewFactory::class, fn(Application $app) => new ViewFactory(
            $app->get(Filesystem::class),
            $app->path('resources/views')
        ));
    }

    public function boot(): void
    {
        //;
    }
}
