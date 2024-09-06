<?php

namespace Navigator\Database\Models;

use ArrayAccess;
use JsonSerializable;
use Navigator\Collections\Arr;
use Navigator\Collections\Collection;
use Navigator\Database\ModelInterface;
use Navigator\Foundation\Concerns\Arrayable;
use WP_Comment;
use WP_Post;
use WP_Site;
use WP_Term;
use WP_User;

class Meta implements Arrayable, ArrayAccess, JsonSerializable
{
    protected string $type;

    public function __construct(protected ModelInterface $model)
    {
        $this->type = $this->resolveType($model);
    }

    public function resolveType(ModelInterface $model): string|int
    {
        $types = [
            WP_Comment::class => 'comment',
            WP_Post::class    => 'post',
            WP_Site::class    => 'blog',
            WP_Term::class    => 'term',
            WP_User::class    => 'user',
        ];

        return $types[$model->object::class];
    }

    protected function unserializeMeta(array $meta): array
    {
        $unserialized = [];

        foreach ($meta as $key => $values) {
            $unserialized[$key] = count($values) == 1
                ? maybe_unserialize($values[0])
                : Arr::map($values, 'maybe_unserialize');
        }

        return $unserialized;
    }

    public function all(bool $unserialize = true): Collection
    {
        $meta = get_metadata($this->type, $this->model->id());

        return Collection::make($unserialize ? $this->unserializeMeta($meta) : $meta);
    }

    public function has(string $key): bool
    {
        return metadata_exists($this->type, $this->model->id(), $key);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if ($field = get_metadata($this->type, $this->model->id(), $key, true)) {
            return is_array($field) ? Collection::make($field) : $field;
        }

        return $default;
    }

    public function set(string $key, mixed $value): void
    {
        update_metadata($this->type, $this->model->id(), $key, $value);
    }

    public function delete(string $key): void
    {
        delete_metadata($this->type, $this->model->id(), $key);
    }

    public function getMany(array $keys = []): array
    {
        $return = [];

        foreach ($keys as $key) {
            $return[$key] = $this->get($key);
        }

        return $return;
    }

    public function setMany(array $attributes = []): void
    {
        foreach ($attributes as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function deleteMany(array $keys = []): void
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
    }

    public function __isset(string $key): bool
    {
        return $this->has($key);
    }

    public function __get(string $key): mixed
    {
        return $this->get($key);
    }

    public function __set(string $key, mixed $value): void
    {
        $this->set($key, $value);
    }

    public function __unset(string $key): void
    {
        $this->delete($key);
    }

    public function offsetExists($key): bool
    {
        return $this->has($key);
    }

    public function offsetGet($key): mixed
    {
        return $this->get($key);
    }

    public function offsetSet($key, mixed $value): void
    {
        $this->set($key, $value);
    }

    public function offsetUnset($key): void
    {
        $this->delete($key);
    }

    public function toArray(): array
    {
        return $this->all()->toArray();
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
