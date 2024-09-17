<?php

namespace Navigator\View;

use Navigator\Events\Dispatcher;
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

        $this->app->singleton(BaseWrapper::class, fn() => new BaseWrapper());
    }

    public function boot(): void
    {
        $this->app->get(Dispatcher::class)->listen(
            'template_include',
            [BaseWrapper::class, 'wrap']
        );
    }
}
