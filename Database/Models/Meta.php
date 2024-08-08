<?php

namespace Navigator\Database\Models;

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

class Meta implements Arrayable, JsonSerializable
{
    protected string $type;

    public function __construct(protected ModelInterface $model)
    {
        $types = [
            WP_Comment::class => 'comment',
            WP_Post::class    => 'post',
            WP_Site::class    => 'blog',
            WP_Term::class    => 'term',
            WP_User::class    => 'user',
        ];

        $this->type = $types[$model->object::class];
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
        return get_metadata($this->type, $this->model->id(), $key, true) ?: $default;
    }

    public function set(string $key, mixed $value): void
    {
        update_metadata($this->type, $this->model->id(), $key, $value);
    }

    public function delete(string $key): void
    {
        delete_metadata($this->type, $this->model->id(), $key);
    }

    public function setMany(array $attributes = []): void
    {
        foreach ($attributes as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function getMany(array $keys = []): array
    {
        $return = [];

        foreach ($keys as $key) {
            $return[$key] = $this->get($key);
        }

        return $return;
    }

    public function deleteMany(array $keys = []): void
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
    }

    public function update(array $meta = []): void
    {
        foreach ($meta as $key => $values) {
            if (in_array($key, ['_edit_lock', '_edit_last'])) {
                continue;
            }

            foreach ($values as $value) {
                $this->set($key, $value);
            }
        }
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
