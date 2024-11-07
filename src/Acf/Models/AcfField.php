<?php

namespace Navigator\Acf\Models;

use ArrayAccess;
use JsonSerializable;
use Navigator\Acf\BlockInterface;
use Navigator\Collections\Collection;
use Navigator\Database\ModelInterface;
use Navigator\Database\Models\Term;
use Navigator\Database\Models\User;
use Navigator\Foundation\Concerns\Arrayable;

class AcfField implements Arrayable, ArrayAccess, JsonSerializable
{
    protected string|int $id = 0;

    public function __construct(protected ModelInterface|BlockInterface $model)
    {
        $this->id = $this->resolveId($model);
    }

    public function resolveId(ModelInterface|BlockInterface $model): string|int
    {
        if ($model instanceof Term) {
            return $model->taxonomy() . '_' . $model->id();
        } elseif ($model instanceof User) {
            return 'user_' . $model->id();
        } else {
            return $model->id();
        }
    }

    public function all(): Collection
    {
        return Collection::make(get_fields($this->id) ?: []);
    }

    public function has(string $key): bool
    {
        return $this->get($key) ? true : false;
    }

    /** @return Collection<int, mixed>|mixed */
    public function get(string $key, mixed $default = null): mixed
    {
        if ($field = get_field($key, $this->id)) {
            return is_array($field) ? Collection::make($field) : $field;
        }

        return $default;
    }

    public function set(string $key, mixed $value): void
    {
        update_field($key, $value, $this->id);
    }

    public function delete(string $key): void
    {
        delete_field($key, $this->id);
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
