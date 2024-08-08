<?php

namespace Navigator\Events;

use Exception;
use Navigator\Contracts\SubscriberInterface;
use Navigator\Foundation\Application;
use ReflectionFunction;
use ReflectionMethod;

class Dispatcher
{
    public function __construct(protected Application $app)
    {
        //
    }

    public function listen(string|array $action, callable|string $listener, int $priority = 10): void
    {
        $listener = $this->resolveListener($listener);
        $parameterCount = $this->getParameterCount($listener);

        foreach ((array) $action as $a) {
            add_action($a, $listener, $priority, $parameterCount);
        }
    }

    public function forget(string|array $action, callable|string $listener, int $priority = 10): void
    {
        $listener = $this->resolveListener($listener);
        $parameterCount = $this->getParameterCount($listener);

        foreach ((array) $action as $a) {
            remove_action($a, $listener, $priority, $parameterCount);
        }
    }

    /**
     * @param class-string<SubscriberInterface> $subscriber
     */
    public function subscribe(string $subscriber): void
    {
        (new $subscriber($this->app))->subscribe($this);
    }

    public function dispatch(string $action, mixed ...$arguments): void
    {
        do_action($action, ...$arguments);
    }

    public function filter(string $action, mixed ...$arguments): mixed
    {
        return apply_filters($action, ...$arguments);
    }

    protected function resolveListener(callable|string $callback): callable
    {
        if (is_string($callback) && class_exists($callback)) {
            return [new $callback($this->app), '__invoke'];
        }

        return $callback;
    }

    protected function getParameterCount(callable $listener): int
    {
        $reflect = is_array($listener)
            ? new ReflectionMethod($listener[0], $listener[1])
            : new ReflectionFunction($listener);

        return $reflect->getNumberOfParameters();
    }
}
