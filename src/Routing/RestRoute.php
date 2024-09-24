<?php

namespace Navigator\Routing;

use Closure;
use Navigator\Collections\Arr;
use Navigator\Http\Concerns\Method;
use Navigator\Http\Exceptions\HttpException;
use Navigator\Http\JsonResponse;
use Navigator\Http\Request;
use Navigator\Http\Response;
use Navigator\Routing\Concerns\HasActionName;
use Navigator\Str\Str;
use Navigator\Validation\Exceptions\ValidationException;
use Throwable;
use Traversable;
use WP_REST_Request;

class RestRoute implements RouteInterface
{
    use HasActionName;

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
            throw new RouteNamespaceException('No namespace specified.');
        }

        register_rest_route($this->namespace(), $this->parsedUri(), [
            'permission_callback' => '__return_true',
            'methods'             => $this->methods(),
            'callback'            => $this->callback($request),
        ]);
    }

    public function methods(): array|string
    {
        return is_array($this->methods) ? Arr::pluck($this->methods, 'value') : $this->methods->value;
    }

    public function namespace(): string
    {
        return Str::of($this->uri)->trim('/')->before('/');
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function parsedUri(): string
    {
        return Str::of($this->uri())
            ->trim('/')
            ->after('/')
            ->replaceMatches('@\/\{([\w]+?)(\?)?\}@', '\/?(?P<$1>[\w\.\_\~\:\?\#\[\]\@\!\$\&\'\(\)\*\+\,\;\=\%\-]+)$2');
    }

    public function callback(Request $request): Closure
    {
        return function (WP_REST_Request $wp) use ($request) {
            $request->merge($wp->get_url_params());

            try {
                $return = call_user_func($this->callback, $request);

                return is_iterable($return) ? iterator_to_array($return) : $return;
            } catch (ValidationException $e) {
                return $e->getResponse();
            } catch (Throwable $e) {
                $statusCode = $e instanceof HttpException ? $e->statusCode : 500;
                $headers = $e instanceof HttpException ? $e->headers : [];

                return $request->expectsJson()
                    ? new JsonResponse(['message' => $e->getMessage()], $statusCode, $headers)
                    : new Response($e->getMessage(), $statusCode, $headers);
            }
        };
    }
}
