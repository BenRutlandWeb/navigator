<?php

namespace Navigator\Routing;

use ArrayObject;
use Generator;
use JsonSerializable;
use Navigator\Collections\Arr;
use Navigator\Foundation\Concerns\Arrayable;
use Navigator\Foundation\Concerns\Htmlable;
use Navigator\Http\Concerns\Method;
use Navigator\Http\Exceptions\HttpException;
use Navigator\Http\JsonResponse;
use Navigator\Http\Request;
use Navigator\Http\Response;
use Navigator\Routing\Concerns\HasActionName;
use Navigator\Validation\Exceptions\ValidationException;
use Stringable;
use Throwable;

class AjaxRoute implements RouteInterface
{
    use HasActionName;

    protected $callback;

    public function __construct(protected string $action, callable $callback)
    {
        $this->callback = $callback;
    }

    public function methods(): array
    {
        return Arr::pluck([Method::GET, Method::POST], 'value');
    }

    public function uri(): string
    {
        return $this->action;
    }

    public function events(): array
    {
        return ['wp_ajax_' . $this->action, 'wp_ajax_nopriv_' . $this->action];
    }

    public function dispatch(Request $request): void
    {
        try {
            $response = call_user_func($this->callback, $request);
        } catch (ValidationException $e) {
            $response = $e->getResponse();
        } catch (Throwable $e) {
            $statusCode = $e instanceof HttpException ? $e->statusCode : 500;
            $headers = $e instanceof HttpException ? $e->headers : [];

            $response = $request->expectsJson()
                ? new JsonResponse(['message' => $e->getMessage()], $statusCode, $headers)
                : new Response($e->getMessage(), $statusCode, $headers);
        }

        $this->resolveResponse($response)->send();

        die();
    }

    public function resolveResponse(mixed $response): Response
    {
        if ($response instanceof Response) {
            return $response;
        }

        if (is_wp_error($response)) {
            return JsonResponse::createFromBase(
                rest_convert_error_to_response($response)
            );
        }

        if ($response instanceof Generator) {
            $response = iterator_to_array($response);
        }

        if (
            $response instanceof Arrayable ||
            $response instanceof ArrayObject ||
            $response instanceof JsonSerializable ||
            is_array($response) ||
            method_exists($response, 'to_array')
        ) {
            return new JsonResponse($response);
        }

        if ($response instanceof Htmlable) {
            $response = $response->toHtml();
        } elseif ($response instanceof Stringable) {
            $response = $response->__toString();
        }

        return new Response($response);
    }
}
