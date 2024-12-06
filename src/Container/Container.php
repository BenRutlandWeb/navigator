<?php

namespace Navigator\Container;

use Closure;
use Psr\Container\ContainerInterface;
use Throwable;

class Container implements ContainerInterface
{
    protected array $bindings = [];

    protected array $instances = [];

    protected array $extenders = [];

    private static self $instance;

    /**
     * @template TInstance
     * @param class-string<TInstance>|string $id
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @return TInstance
     */
    public function get(string $id): mixed
    {
        try {
            return $this->resolve($id);
        } catch (Throwable $e) {
            if ($this->has($id)) {
                throw new ContainerException($id, $e->getCode(), $e);
            }

            throw new NotFoundException($id, $e->getCode(), $e);
        }
    }

    public function has(string $id): bool
    {
        return isset($this->bindings[$id]) || isset($this->instances[$id]);
    }

    /**
     * @template TInstance
     * @param class-string<TInstance>|string $id
     * @throws ContainerExceptionInterface
     * @return TInstance|mixed
     */
    public function resolve(string $id, mixed ...$args): mixed
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        [$binding, $shared] = $this->bindings[$id];

        if ($binding instanceof Closure) {
            $instance = $binding($this, ...$args);

            foreach ($this->extenders[$id] ?? [] as $extender) {
                $instance = $extender($instance, $this);
            }

            if ($shared) {
                $this->instances[$id] = $instance;
            }

            return $instance;
        }

        throw new ContainerException($id);
    }

    /** @throws ContainerExceptionInterface */
    public function singleton(string $id, ?Closure $binding = null): void
    {
        $this->bind($id, $binding, true);
    }

    /** @throws ContainerExceptionInterface */
    public function bind(string $id, ?Closure $binding = null, bool $shared = false): void
    {
        if (!$binding && !class_exists($id)) {
            throw new ContainerException("Target class [$id] does not exist.");
        }

        $this->bindings[$id] = [$binding ?? fn() => new $id(), $shared];
    }

    /**
     * @template TInstance
     * @param TInstance $instance
     * @return TInstance
     */
    public function instance(string $id, mixed $instance): mixed
    {
        $this->instances[$id] = $instance;

        return $instance;
    }

    public function extend(string $id, Closure $callback): void
    {
        if (isset($this->instances[$id])) {
            $this->instances[$id] = $callback($this->instances[$id], $this);
        } else {
            $this->extenders[$id][] = $callback;
        }
    }

    public static function getInstance(): static
    {
        return self::$instance;
    }

    public static function setInstance(self $instance): void
    {
        self::$instance = $instance;
    }
}
