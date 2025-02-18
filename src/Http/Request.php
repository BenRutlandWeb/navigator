<?php

namespace Navigator\Http;

use JsonSerializable;
use Navigator\Collections\Arr;
use Navigator\Collections\Collection;
use Navigator\Database\ModelInterface;
use Navigator\Database\Models\User;
use Navigator\Database\Relation;
use Navigator\Foundation\Concerns\Arrayable;
use Navigator\Http\Concerns\HasCookies;
use Navigator\Http\Concerns\HasHeaders;
use Navigator\Http\Concerns\HasServer;
use Navigator\Http\Concerns\Method;
use Navigator\Str\Str;
use Navigator\Validation\ValidationFactory;
use Throwable;
use UnitEnum;
use WP_REST_Request;

class Request extends WP_REST_Request implements Arrayable, JsonSerializable
{
    use HasCookies;
    use HasHeaders;
    use HasServer;

    /** @var (callable(): User) */
    protected static $userResolver;

    /** @var (callable(): ValidationFactory) */
    protected static $validatorResolver;

    /** @var (callable(): Url) */
    protected static $urlResolver;

    public function __construct(array $query = [], array $request = [], array $cookies = [], array $files = [], array $server = [], array $attributes = [])
    {
        parent::__construct($server['REQUEST_METHOD'], $server['PATH_INFO'] ?? '/');
        $this->set_query_params(wp_unslash($query));
        $this->set_body_params(wp_unslash($request));
        $this->set_file_params($files);
        $this->set_headers($this->getHeadersFromServer(wp_unslash($server)));
        $this->set_body(file_get_contents('php://input'));
        $this->set_cookie_params($cookies);
        $this->set_server_params($server);
        $this->set_url_params($attributes);
    }

    protected function getHeadersFromServer(array $server): array
    {
        $headers = [];

        foreach ($server as $key => $value) {
            if (Str::startsWith($key, 'HTTP_')) {
                $headers[substr($key, 5)] = $value;
            } elseif ($key === 'REDIRECT_HTTP_AUTHORIZATION' && empty($server['HTTP_AUTHORIZATION'])) {
                /*
				 * In some server configurations, the authorization header is passed in this alternate location.
				 * Since it would not be passed in in both places we do not check for both headers and resolve.
				 */
                $headers['AUTHORIZATION'] = $value;
            } elseif (in_array($key, ['CONTENT_LENGTH', 'CONTENT_MD5', 'CONTENT_TYPE', 'PHP_AUTH_USER', 'PHP_AUTH_PW'])) {
                $headers[$key] = $value;
            }
        }

        return $headers;
    }

    public static function capture(): static
    {
        return new static($_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);
    }

    public static function fromBase(WP_REST_Request $base): static
    {
        return new static(
            $base->get_query_params(),
            $base->get_body_params(),
            $_COOKIE,
            $base->get_file_params(),
            $_SERVER,
            $base->get_url_params(),
        );
    }

    public function method(): Method
    {
        $realMethod = $this->realMethod();

        if ($realMethod == Method::POST) {
            $method = $this->get_header('x-http-method-override') ?? $this->input('_method', 'POST');

            return Method::tryFrom(Str::upper($method)) ?? Method::POST;
        }

        return $realMethod;
    }

    public function realMethod(): Method
    {
        return Method::tryFrom($this->get_method());
    }

    public function merge(array $attributes = []): static
    {
        foreach ($attributes as $key => $value) {
            $this->set_param($key, $value);
        }

        return $this;
    }

    public function isSecure(): bool
    {
        return is_ssl();
    }

    public function isJson(): bool
    {
        return $this->is_json_content_type();
    }

    public function expectsJson(): bool
    {
        return $this->ajax() || $this->wantsJson();
    }

    public function ajax(): bool
    {
        return wp_doing_ajax();
    }

    public function wantsJson(): bool
    {
        return (defined('REST_REQUEST') && constant('REST_REQUEST')) || wp_is_json_request();
    }

    public function bearerToken(): ?string
    {
        $header = Str::of($this->header('authorization', ''));

        if ($header->startsWith('Bearer ')) {
            return $header->substr(7);
        }

        return null;
    }

    public function url(): string
    {
        return strtok($this->fullUrl(), '?');
    }

    public function fullUrl(): string
    {
        return home_url($this->server('REQUEST_URI'));
    }

    public function has(string $key): bool
    {
        return $this->has_param($key);
    }

    public function missing(string $key): bool
    {
        return !$this->has($key);
    }

    public function filled(string $key): bool
    {
        return $this->has($key) && $this->input($key) !== '';
    }

    public function isNotFilled(string $key): bool
    {
        return !$this->filled($key);
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->get_param($key) ?? $default;
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->get_query_params()[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $this->get_body_params()[$key] ?? $default;
    }

    public function keys(): array
    {
        return Arr::keys($this->get_params());
    }

    public function all(): array
    {
        return $this->get_params();
    }

    public function toArray(): array
    {
        return $this->all();
    }

    public function jsonSerialize(): array
    {
        return $this->all();
    }

    public function boolean(string $key, mixed $default = false): bool
    {
        return filter_var($this->input($key, $default), FILTER_VALIDATE_BOOLEAN);
    }

    public function integer(string $key, int $default = 0): int
    {
        return intval($this->input($key, $default));
    }

    public function float(string $key, float $default = 0.0): float
    {
        return floatval($this->input($key, $default));
    }

    /**
     * @template TEnum of UnitEnum
     * @param class-string<TEnum> $enum
     * @return ?TEnum
     */
    public function enum(string $key, string $enum): ?UnitEnum
    {
        $input = $this->input($key);

        if (!$input || !enum_exists($enum)) {
            return null;
        }

        try {
            if (method_exists($enum, 'tryFrom')) {
                return call_user_func([$enum, 'tryFrom'], $input);
            }

            return $enum::{$input} ?? null;
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * @template TModelInterface
     * @param class-string<TModelInterface> $model
     * @return ?TModelInterface
     */
    public function model(string $key, string $model): ?ModelInterface
    {
        if ($value = $this->integer($key)) {
            return $model::findOrFail($value);
        }

        return null;
    }

    public function collect(?string $key = null): Collection
    {
        return Collection::make($key ? $this->input($key) : $this->all());
    }

    public function user(): ?User
    {
        return call_user_func(static::$userResolver ?? function () {
            //
        });
    }

    /** @param (callable(): ?User) $callback */
    public function setUserResolver(callable $callback): void
    {
        static::$userResolver = $callback;
    }

    /** @param (callable(): ValidationFactory) $callback */
    public function setValidatorResolver(callable $callback): void
    {
        static::$validatorResolver = $callback;
    }

    public function validate(array $rules, array $messages = []): array
    {
        /** @var ValidationFactory $validator */
        $validator = call_user_func(static::$validatorResolver);

        return $validator->make($this->all(), $rules, $messages)->validate();
    }

    /** @param (callable(): Url) $callback */
    public function setUrlResolver(callable $callback): void
    {
        static::$urlResolver = $callback;
    }

    public function hasValidSignature(): bool
    {
        /** @var Url $validator */
        $url = call_user_func(static::$urlResolver);

        return $url->hasValidSignature($this);
    }
}
