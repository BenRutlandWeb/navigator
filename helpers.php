<?php

use Carbon\CarbonImmutable;
use Navigator\Auth\Auth;
use Navigator\Cache\Repository;
use Navigator\Collections\Collection;
use Navigator\Encryption\Encrypter;
use Navigator\Foundation\Application;
use Navigator\Foundation\Mix;
use Navigator\Hashing\Hasher;
use Navigator\Http\Client\Http;
use Navigator\Http\Request;
use Navigator\Http\Response;
use Navigator\Http\ResponseFactory;
use Navigator\Http\Url;
use Navigator\Mail\Mailer;
use Navigator\Str\Str;
use Navigator\Str\Stringable;
use Navigator\View\View;
use Navigator\View\ViewFactory;
use WP_Queue\Queue;

if (!function_exists('abort')) {
    function abort(int $code, string $message = '', array $headers = []): void
    {
        app()->abort($code, $message, $headers);
    }
}

if (!function_exists('app')) {
    /**
     * @template T
     * @param class-string<T>|null $id
     * @param mixed ...$args
     * @return T|Application
     */
    function app(?string $id = null, mixed ...$args): mixed
    {
        $app = Application::getInstance();

        return $id ? $app->resolve($id, ...$args) : $app;
    }
}

if (!function_exists('auth')) {
    function auth(): Auth
    {
        return app(Auth::class);
    }
}

if (!function_exists('cache')) {
    /** @return Repository|mixed */
    function cache(?string $key = null, mixed $default = null): mixed
    {
        $cache = app(Repository::class);

        return $key ? $cache->get($key, $default) : $cache;
    }
}

if (!function_exists('collect')) {
    function collect(array $items = []): Collection
    {
        return Collection::make($items);
    }
}

if (!function_exists('config')) {
    /** @return Repository|mixed */
    function config(?string $key = null, mixed $default = null): mixed
    {
        return app()->config($key, $default);
    }
}

if (!function_exists('decrypt')) {
    function decrypt(string $payload, bool $unserialize = true): mixed
    {
        return app(Encrypter::class)->decrypt($payload, $unserialize);
    }
}

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        return app()->env($key, $default);
    }
}

if (!function_exists('encrypt')) {
    function encrypt(mixed $value, bool $serialize = true): string
    {
        return app(Encrypter::class)->encrypt($value, $serialize);
    }
}

if (!function_exists('hasher')) {
    function hasher(): Hasher
    {
        return app(Hasher::class);
    }
}

if (!function_exists('http')) {
    function http(): Http
    {
        return app(Http::class);
    }
}

if (!function_exists('mailer')) {
    function mailer(): Mailer
    {
        return app(Mailer::class);
    }
}

if (!function_exists('mix')) {
    function mix(string $path): string
    {
        return app(Mix::class)->path($path);
    }
}

if (!function_exists('now')) {
    function now(DateTimeZone|string|null $tz = null): CarbonImmutable
    {
        return CarbonImmutable::now($tz);
    }
}

if (!function_exists('queue')) {
    function queue(): Queue
    {
        return app(Queue::class);
    }
}

if (!function_exists('request')) {
    function request(): Request
    {
        return app(Request::class);
    }
}

if (!function_exists('response')) {
    function response(mixed $content = null, int $status = 200, array $headers = []): ResponseFactory|Response
    {
        $factory = app(ResponseFactory::class);

        return $content ? $factory->make($content, $status, $headers) : $factory;
    }
}

if (!function_exists('str')) {
    function str(string $string): Stringable
    {
        return new Stringable($string);
    }
}

if (!function_exists('svg')) {
    function svg(string $svg): string
    {
        $path = app()->config('app.asset_url') . Str::finish($svg, '.svg');

        $file = Str::trim(ABSPATH, '/') . wp_make_link_relative($path);

        return file_exists($file) ? file_get_contents($file) : '';
    }
}

if (!function_exists('today')) {
    function today(DateTimeZone|string|null $tz = null): CarbonImmutable
    {
        return CarbonImmutable::today($tz);
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): Url|string
    {
        $url = app(Url::class);

        return $path ? $url->home($path) : $url;
    }
}

if (!function_exists('view')) {
    function view(?string $path = null, array $data = []): ViewFactory|View
    {
        $factory = app(ViewFactory::class);

        return $path ? $factory->make($path, $data) : $factory;
    }
}

if (!function_exists('yesterday')) {
    function yesterday(DateTimeZone|string|null $tz = null): CarbonImmutable
    {
        return CarbonImmutable::yesterday($tz);
    }
}
