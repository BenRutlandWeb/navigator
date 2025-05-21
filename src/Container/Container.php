<?php

namespace Navigator\Container;

use Closure;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionParameter;
use Throwable;

class Container implements ContainerInterface
{
    protected array $bindings = [];

    protected array $instances = [];

    protected array $extenders = [];

    private static self $instance;

    /**
     * @template TGet
     * @param class-string<TGet>|string $id
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @return ($id is class-string<TGet> ? TGet : mixed)
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
     * @template TResolve
     * @param class-string<TResolve>|string $id
     * @throws ContainerExceptionInterface
     * @return ($id is class-string<TResolve> ? TResolve : mixed)
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

    /**
     * @template TMake
     * @param class-string<TMake>|string $id
     * @return ($id is class-string<TMake> ? TMake : mixed)
     */
    public function make(string $id, array $args = []): mixed
    {
        return $this->has($id) ? $this->get($id) : $this->build($id, $args);
    }

    /**
     * @template TBuild
     * @param class-string<TBuild> $id
     * @throws BindingResolutionException
     * @return TBuild
     */
    public function build(string $id, array $args = []): mixed
    {
        try {
            $reflection = new ReflectionClass($id);
        } catch (ReflectionException $e) {
            throw new BindingResolutionException("Target class [$id] does not exist.", 0, $e);
        }

        if (!$reflection->isInstantiable()) {
            throw new BindingResolutionException("Target [$id] is not instantiable.");
        }

        if ($constructor = $reflection->getConstructor()) {
            return $reflection->newInstanceArgs(
                $this->resolveDependencies($constructor, $args)
            );
        }

        return $reflection->newInstanceWithoutConstructor();
    }

    /**
     * @template TCall
     * @param (callable(...): TCall) $callable
     * @return TCall
     */
    public function call(callable $callable, array $args = []): mixed
    {
        if (is_array($callable)) {
            $reflection = new ReflectionMethod(...$callable);

            return $reflection->invokeArgs(
                $callable[0],
                $this->resolveDependencies($reflection, $args)
            );
        }

        $reflection = new ReflectionFunction($callable);

        return $reflection->invokeArgs(
            $this->resolveDependencies($reflection, $args)
        );
    }

    protected function resolveDependencies(ReflectionFunctionAbstract $reflection, array $args = []): array
    {
        return array_map(function (ReflectionParameter $parameter) use ($args) {
            $name = $parameter->getName();

            if ($args[$name] ?? null) {
                return $args[$name];
            }

            return $this->resolveDependency($parameter, $args);
        }, $reflection->getParameters());
    }

    protected function resolveDependency(ReflectionParameter $parameter, array $args = []): mixed
    {
        if (($type = $parameter->getType()) && !$type->isBuiltin()) {
            if ($instance = $this->make($type->getName(), $args)) {
                return $instance;
            }
        }

        $declaringClass = $parameter->getDeclaringClass()->getName();

        return $parameter->isDefaultValueAvailable()
            ? $parameter->getDefaultValue()
            : throw new BindingResolutionException("Unresolvable dependency resolving [$parameter] in class {$declaringClass}");
    }

    /** @throws ContainerExceptionInterface */
    public function singleton(string $id, ?Closure $binding = null): void
    {
        $this->bind($id, $binding, true);
    }

    /** @throws ContainerExceptionInterface */
    public function bind(string $id, ?Closure $binding = null, bool $shared = false): void
    {
        $this->bindings[$id] = [$binding ?? fn($app, ...$args) => $this->build($id, $args), $shared];
    }

    /**
     * @template TInstance
     * @param TInstance $instance
     * @return TInstance
     */
    public function instance(string $id, mixed $instance): mixed
    {
        foreach ($this->extenders[$id] ?? [] as $extender) {
            $instance = $extender($instance, $this);
        }

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
