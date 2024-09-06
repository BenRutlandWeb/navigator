<?php

namespace Navigator\Http;

use Navigator\Foundation\Application;
use Navigator\Foundation\ServiceProvider;
use Navigator\Http\Client\Http;
use Navigator\View\ViewFactory;

class HttpServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Url::class, fn(Application $app) => new Url(
            $app,
            $app->get(Request::class)
        ));

        $this->app->singleton(
            ResponseFactory::class,
            fn() => new ResponseFactory($this->app->get(ViewFactory::class))
        );

        $this->app->bind(Http::class, fn() => new Http());
    }

    public function boot(): void
    {
        //
    }
}
