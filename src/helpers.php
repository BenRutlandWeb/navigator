<?php

namespace Navigator;

use Carbon\CarbonImmutable;
use DateTimeZone;
use Faker\Generator as Faker;
use Navigator\Auth\Auth;
use Navigator\Cache\Repository;
use Navigator\Collections\Collection;
use Navigator\Encryption\Encrypter;
use Navigator\Foundation\Application;
use Navigator\Foundation\Mix;
use Navigator\Hashing\Hash;
use Navigator\Hashing\HasherInterface;
use Navigator\Hashing\HashManager;
use Navigator\Http\Client\Http;
use Navigator\Http\Concerns\Method;
use Navigator\Http\Request;
use Navigator\Http\Response;
use Navigator\Http\ResponseFactory;
use Navigator\Http\Url;
use Navigator\Mail\MailFactory;
use Navigator\Str\Str;
use Navigator\Str\Stringable;
use Navigator\Validation\ValidationFactory;
use Navigator\Validation\Validator;
use Navigator\View\View;
use Navigator\View\ViewFactory;
use WP_Queue\Queue;

function abort(int $code, string $message = '', array $headers = []): void
{
    app()->abort($code, $message, $headers);
}

/**
 * @template T
 * @param class-string<T>|null $id
 * @param mixed ...$args
 * @return ($id is null ? Application : T)
 */
function app(?string $id = null, mixed ...$args): mixed
{
    $app = Application::getInstance();

    return $id ? $app->resolve($id, ...$args) : $app;
}

function auth(): Auth
{
    return app(Auth::class);
}

/** @return Repository|mixed */
function cache(?string $key = null, mixed $default = null): mixed
{
    $cache = app(Repository::class);

    return $key ? $cache->get($key, $default) : $cache;
}

function collect(array $items = []): Collection
{
    return Collection::make($items);
}

/** @return Repository|mixed */
function config(?string $key = null, mixed $default = null): mixed
{
    return app()->config($key, $default);
}

function csrf_field(): string
{
    return wp_nonce_field('wp_rest', '_token', true, false);
}

function csrf_token(): string
{
    return wp_create_nonce('wp_rest');
}

function decrypt(string $payload, bool $unserialize = true): mixed
{
    return app(Encrypter::class)->decrypt($payload, $unserialize);
}

function env(string $key, mixed $default = null): mixed
{
    return app()->env($key, $default);
}

function encrypt(mixed $value, bool $serialize = true): string
{
    return app(Encrypter::class)->encrypt($value, $serialize);
}

function fake(?string $locale = null): Faker
{
    return app(Faker::class, $locale);
}

function hasher(?Hash $driver = null): HashManager|HasherInterface
{
    $manager = app(HashManager::class);

    return $driver ? $manager->driver($driver) : $manager;
}

function http(): Http
{
    return app(Http::class);
}

function is_front_end_request(): bool
{
    return !is_admin()
        && !wp_doing_ajax()
        && !wp_is_json_request()
        && !wp_doing_cron()
        && !app()->runningInConsole();
}

function mailer(): MailFactory
{
    return app(MailFactory::class);
}

function method_field(Method $method): string
{
    return sprintf('<input type="hidden" name="_method" value="%s" />', $method->value);
}

function mix(string $path): string
{
    return app(Mix::class)->path($path);
}

function now(DateTimeZone|string|null $tz = null): CarbonImmutable
{
    return CarbonImmutable::now($tz);
}

function queue(): Queue
{
    return app(Queue::class);
}

function request(): Request
{
    return app(Request::class);
}

function response(mixed $content = null, int $status = 200, array $headers = []): ResponseFactory|Response
{
    $factory = app(ResponseFactory::class);

    return $content ? $factory->make($content, $status, $headers) : $factory;
}

function str(string $string): Stringable
{
    return new Stringable($string);
}

function svg(string $svg): string
{
    $path = app()->assetUrl(Str::finish($svg, '.svg'));

    $file = Str::trim(ABSPATH, '/') . wp_make_link_relative($path);

    return file_exists($file) ? file_get_contents($file) : '';
}

function today(DateTimeZone|string|null $tz = null): CarbonImmutable
{
    return CarbonImmutable::today($tz);
}

function url(string $path = ''): Url|string
{
    $url = app(Url::class);

    return $path ? $url->home($path) : $url;
}

function validator(?array $input = null, array $rules = [], array $messages = []): ValidationFactory|Validator
{
    $factory = app(ValidationFactory::class);

    return !is_null($input) ? $factory->make($input, $rules, $messages) : $factory;
}

function view(?string $path = null, array $data = []): ViewFactory|View
{
    $factory = app(ViewFactory::class);

    return $path ? $factory->make($path, $data) : $factory;
}

function yesterday(DateTimeZone|string|null $tz = null): CarbonImmutable
{
    return CarbonImmutable::yesterday($tz);
}
