<?php

namespace Navigator\Routing;

use Navigator\Collections\Collection;
use Navigator\Events\Dispatcher;
use Navigator\Http\Concerns\Method;
use Navigator\Http\Exceptions\HttpException;
use Navigator\Http\Request;

class Router
{
    /** @param array<int, RouteInterface> $routes */
    public function __construct(protected Dispatcher $dispatcher, protected array $routes = [])
    {
        //
    }

    public function ajax(string $action, callable|array $callback): AjaxRoute
    {
        return $this->addRoute(new AjaxRoute($action, $this->resolveCallback($callback)));
    }

    public function get(string $uri, callable|array $callback): RestRoute
    {
        return $this->addRestRoute(Method::GET, $uri, $callback);
    }

    public function post(string $uri, callable|array $callback): RestRoute
    {
        return $this->addRestRoute(Method::POST, $uri, $callback);
    }

    public function put(string $uri, callable|array $callback): RestRoute
    {
        return $this->addRestRoute(Method::PUT, $uri, $callback);
    }

    public function patch(string $uri, callable|array $callback): RestRoute
    {
        return $this->addRestRoute(Method::PATCH, $uri, $callback);
    }

    public function delete(string $uri, callable|array $callback): RestRoute
    {
        return $this->addRestRoute(Method::DELETE, $uri, $callback);
    }

    /** @param Method|array<int, Method> $methods */
    public function matches(Method|array $methods, string $uri, callable|array $callback): RestRoute
    {
        return $this->addRestRoute($methods, $uri, $callback);
    }

    public function any(string $uri, callable|array $callback): RestRoute
    {
        return $this->addRestRoute(Method::cases(), $uri, $callback);
    }

    /** @param Method|array<int, Method> $methods */
    public function addRestRoute(Method|array $methods, string $uri, callable|array $callback): RouteInterface
    {
        return $this->addRoute(new RestRoute($methods, $uri, $this->resolveCallback($callback)));
    }

    public function addRoute(RouteInterface $route): RouteInterface
    {
        return $this->routes[] = $route;
    }

    public function dispatch(Request $request): void
    {
        foreach ($this->routes as $route) {
            $this->dispatcher->listen($route->events(), function () use ($route, $request) {
                return $route->dispatch($request);
            });
        }
    }

    public function resolveCallback(callable|array $callback): callable
    {
        if (is_callable($callback)) {
            return $callback;
        }

        try {
            [$class, $method] = $callback;

            $callback = [new $class(), $method];

            return is_callable($callback) ? $callback : $this->handleException($class, $method);
        } catch (\Throwable $e) {
            return $this->handleException($class, $method);
        }
    }

    public function handleException(string $class, string $method): callable
    {
        return fn() => throw new HttpException(500, sprintf('%s::%s is invalid.', $class, $method));
    }

    /** @return Collection<int, RouteInterface> */
    public function getRoutes(): Collection
    {
        return Collection::make($this->routes);
    }
}
