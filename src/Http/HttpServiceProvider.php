<?php

namespace Navigator\Http;

use Navigator\Encryption\Exceptions\MissingAppKeyException;
use Navigator\Foundation\Application;
use Navigator\Foundation\ServiceProvider;
use Navigator\Http\Client\Http;
use Navigator\Str\Str;
use Navigator\View\ViewFactory;

class HttpServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Url::class, fn(Application $app) => new Url(
            $app,
            $app->get(Request::class),
            $this->parseKey($app->env('APP_KEY')),
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

    protected function parseKey(string $key): string
    {
        if (empty($key)) {
            throw new MissingAppKeyException();
        }

        $key = Str::of($key);

        if ($key->startsWith($prefix = 'base64:')) {
            $key = $key->replace($prefix, '')->fromBase64();
        }

        return $key;
    }
}
