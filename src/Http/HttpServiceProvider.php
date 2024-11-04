<?php

namespace Navigator\Http;

use Navigator\Foundation\Application;
use Navigator\Foundation\ServiceProvider;
use Navigator\Hashing\Hasher;
use Navigator\Http\Client\Http;
use Navigator\View\ViewFactory;

class HttpServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Url::class, fn(Application $app) => new Url(
            $app->assetUrl(),
            $app->get(Request::class),
            $app->get(Hasher::class),
        ));

        $this->app->singleton(
            ResponseFactory::class,
            fn() => new ResponseFactory($this->app->get(ViewFactory::class))
        );

        $this->app->bind(Http::class, fn() => new Http());

        $this->app->rebinding(Request::class, function (Application $app, Request $request) {
            $request->setUrlResolver(fn() => $app->get(Url::class));
        });
    }

    public function boot(): void
    {
        //
    }
}
