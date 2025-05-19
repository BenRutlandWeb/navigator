<?php

namespace Navigator\Events;

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

    public function listen(string|array $action, callable|string $listener): void
    {
        $listener = $this->resolveListener($listener);

        [$priority, $parameterCount] = $this->reflect($listener);

        foreach ((array) $action as $a) {
            add_action($a, $listener, $priority, $parameterCount);
        }
    }

    public function forget(string|array $action, callable|string $listener): void
    {
        $listener = $this->resolveListener($listener);

        [$priority, $parameterCount] = $this->reflect($listener);

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

    protected function reflect(callable $listener): array
    {
        $reflect = is_array($listener)
            ? new ReflectionMethod($listener[0], $listener[1])
            : new ReflectionFunction($listener);

        $attribute = $reflect->getAttributes(Priority::class)[0] ?? null;

        return [
            $attribute ? $attribute->newInstance()->priority : 10,
            $reflect->getNumberOfParameters(),
        ];
    }
}
