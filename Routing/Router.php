<?php

namespace Navigator\Routing;

use Navigator\Events\Dispatcher;
use Navigator\Http\Concerns\Method;
use Navigator\Http\Request;

class Router
{
    /** @param array<int, RouteInterface> $routes */
    public function __construct(protected Dispatcher $dispatcher, protected array $routes = [])
    {
        //
    }

    public function ajax(string $action, callable $callback): AjaxRoute
    {
        return $this->addRoute(new AjaxRoute($action, $callback));
    }

    public function get(string $uri, callable $callback): RestRoute
    {
        return $this->addRoute(new RestRoute(Method::GET, $uri, $callback));
    }

    public function post(string $uri, callable $callback): RestRoute
    {
        return $this->addRoute(new RestRoute(Method::POST, $uri, $callback));
    }

    public function put(string $uri, callable $callback): RestRoute
    {
        return $this->addRoute(new RestRoute(Method::PUT, $uri, $callback));
    }

    public function patch(string $uri, callable $callback): RestRoute
    {
        return $this->addRoute(new RestRoute(Method::PATCH, $uri, $callback));
    }

    public function delete(string $uri, callable $callback): RestRoute
    {
        return $this->addRoute(new RestRoute(Method::DELETE, $uri, $callback));
    }

    /** @param Method|array<int, Method> $methods */
    public function matches(Method|array $methods, string $uri, callable $callback): RestRoute
    {
        return $this->addRoute(new RestRoute($methods, $uri, $callback));
    }

    public function any(string $uri, callable $callback): RestRoute
    {
        return $this->addRoute(new RestRoute(Method::cases(), $uri, $callback));
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
}
