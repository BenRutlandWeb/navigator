<?php

namespace Navigator\Database\Factories;

use Faker\Generator;
use InvalidArgumentException;
use Navigator\Collections\Arr;
use Navigator\Collections\Collection;
use Navigator\Database\ModelInterface;
use Navigator\Database\Models\Comment;
use Navigator\Database\Models\Post;
use Navigator\Database\Models\Term;
use Navigator\Database\Models\User;

/** @template T of ModelInterface */
abstract class Factory
{
    /** @var (callable(): Generator) */
    protected static $fakerResolver;

    protected Generator $faker;

    /** @param class-string<T> $model */
    public function __construct(
        protected string $model,
        protected array $states = [],
        protected ?int $count = null
    ) {
        $this->resolveFaker();
    }

    /** @param class-string<T> $model */
    public function newInstance(string $model, array $states = [], ?int $count = null): static
    {
        return new static($model, $states, $count);
    }

    /** @return T */
    abstract public function newModel(array $attributes = []): ModelInterface;

    abstract public function definition(): array;

    public function count(int $count): static
    {
        return $this->newInstance($this->model, $this->states, $count);
    }

    public function with(array $attributes = []): static
    {
        return $this->newInstance($this->model, Arr::merge($this->states, $attributes), $this->count);
    }

    public function getAttributes(): array
    {
        return Arr::merge($this->definition(), $this->states);
    }

    /** @return T|Collection<int, T> */
    public function make(array $attributes = []): ModelInterface|Collection
    {
        if (!empty($attributes)) {
            return $this->with($attributes)->make();
        }

        if (is_null($this->count)) {
            return $this->newModel($this->getAttributes());
        }

        return Collection::range(1, $this->count)->map(
            fn() => $this->newModel($this->getAttributes())
        );
    }

    /** @param (callable(): Generator) $resolver */
    public static function setFakerResolver(callable $resolver): void
    {
        static::$fakerResolver = $resolver;
    }

    public function resolveFaker(): void
    {
        $this->faker = call_user_func(static::$fakerResolver);
    }

    public static function forModel(string $model): static
    {
        if (is_subclass_of($model, Post::class)) {
            return new PostFactory($model);
        } elseif (is_subclass_of($model, Comment::class)) {
            return new CommentFactory($model);
        } elseif (is_subclass_of($model, Term::class)) {
            return new TermFactory($model);
        } elseif (is_subclass_of($model, User::class)) {
            return new UserFactory($model);
        }

        throw new InvalidArgumentException("{$model} does not have a corresponding factory.");
    }
}
