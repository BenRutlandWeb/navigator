<?php

namespace Navigator\Routing;

use Atomic\Http\Response;
use Navigator\Collections\Arr;
use Navigator\Http\Concerns\Method;
use Navigator\Http\Exceptions\HttpException;
use Navigator\Http\JsonResponse;
use Navigator\Http\Request;
use Navigator\Str\Str;
use Throwable;
use WP_REST_Request;

class RestRoute implements RouteInterface
{
    protected $callback;

    /** @param Method|array<int, Method> $methods*/
    public function __construct(protected Method|array $methods, protected string $uri, callable $callback)
    {
        $this->callback = $callback;
    }

    public function events(): array
    {
        return ['rest_api_init'];
    }

    public function dispatch(Request $request): void
    {
        if (!Str::contains($this->uri, '/')) {
            throw new \Exception('No namespace specified.');
        }

        register_rest_route($this->namespace(), $this->uri(), [
            'permission_callback' => '__return_true',
            'methods' => is_array($this->methods) ? Arr::pluck($this->methods, 'value') : $this->methods->value,
            'callback' => function (WP_REST_Request $wp) use ($request) {

                $request->merge($wp->get_url_params());

                try {
                    return call_user_func($this->callback, $request);
                } catch (Throwable $e) {
                    $statusCode = $e instanceof HttpException ? $e->statusCode : 500;
                    $headers = $e instanceof HttpException ? $e->headers : [];

                    return $request->expectsJson()
                        ? new JsonResponse(['message' => $e->getMessage()], $statusCode, $headers)
                        : new Response($e->getMessage(), $statusCode, $headers);
                }
            },
        ]);
    }

    public function namespace(): string
    {
        return Str::of($this->uri)->trim('/')->before('/');
    }

    public function uri(): string
    {
        return Str::of($this->uri)
            ->trim('/')
            ->after('/')
            ->replaceMatches('@\/\{([\w]+?)(\?)?\}@', '\/?(?P<$1>[\w-]+)$2');
    }
}
