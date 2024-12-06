<?php

namespace Navigator\Http;

use Navigator\Foundation\Application;
use Navigator\Foundation\ServiceProvider;
use Navigator\Hashing\Drivers\HmacHasher;
use Navigator\Http\Client\Http;
use Navigator\View\ViewFactory;

class HttpServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->instance(Request::class, Request::capture());

        $this->app->singleton(Url::class, fn(Application $app) => new Url(
            $app->assetUrl(),
            $app->get(Request::class),
            $app->get(HmacHasher::class),
        ));

        $this->app->singleton(
            ResponseFactory::class,
            fn() => new ResponseFactory($this->app->get(ViewFactory::class))
        );

        $this->app->bind(Http::class, fn() => new Http());

        $this->app->extend(Request::class, function (Request $request, Application $app) {
            $request->setUrlResolver(fn() => $app->get(Url::class));

            return $request;
        });
    }

    public function boot(): void
    {
        //
    }
}
