<?php

namespace Navigator\Container;

use Closure;
use Psr\Container\ContainerInterface;
use Throwable;

class Container implements ContainerInterface
{
    protected array $bindings = [];

    protected array $instances = [];

    protected array $resolved = [];

    protected array $reboundCallbacks = [];

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

    public function resolved(string $id): bool
    {
        return isset($this->resolved[$id]) || isset($this->instances[$id]);
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

            if ($shared) {
                $this->instances[$id] = $instance;
            }

            $this->resolved[$id] = true;

            return $instance;
        }

        throw new ContainerException($id);
    }

    public function singleton(string $id, Closure $binding): void
    {
        $this->bind($id, $binding, true);
    }

    public function bind(string $id, Closure $binding, bool $shared = false): void
    {
        $this->bindings[$id] = [$binding, $shared];

        if ($this->resolved($id)) {
            $this->rebound($id);
        }
    }

    /**
     * @template TInstance
     * @param TInstance $instance
     * @return TInstance
     */
    public function instance(string $id, mixed $instance): mixed
    {
        $this->instances[$id] = $instance;

        if ($this->has($id)) {
            $this->rebound($id);
        }

        return $instance;
    }

    protected function rebound(string $id): void
    {
        $instance = $this->resolve($id);

        foreach ($this->reboundCallbacks[$id] ?? [] as $callback) {
            $callback($this, $instance);
        }
    }

    public function rebinding(string $id, Closure $callback): void
    {
        $this->reboundCallbacks[$id][] = $callback;

        if ($this->has($id)) {
            $this->resolve($id);
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
