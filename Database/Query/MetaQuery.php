<?php

namespace Navigator\Database\Query;

use Closure;
use Navigator\Collections\Arr;
use Navigator\Database\Query\Concerns\Relation;
use Navigator\Foundation\Concerns\Arrayable;

class MetaQuery implements Arrayable
{
    public function __construct(protected array $query = [])
    {
        //
    }

    public static function make(array $query = []): static
    {
        return new static($query);
    }

    public function where(string|callable $key, ?string $compare = null, mixed $value = null, ?string $type = null, ?string $named_key = null, Relation $relation = Relation::AND): static
    {
        $this->query['relation'] = $relation->value;

        if (is_callable($key)) {
            if ($named_key) {
                $this->query[$named_key] = $subQuery = new static;
            } else {
                $this->query[] = $subQuery = new static;
            }
            $key($subQuery);

            return $this;
        }

        $attributes = Arr::filter(compact('key', 'compare', 'value', 'type'), function ($entry) {
            return !is_null($entry);
        });

        if ($named_key) {
            $this->query[$named_key] = $attributes;
        } else {
            $this->query[] = $attributes;
        }

        return $this;
    }

    public function orWhere(string|callable $key, ?string $compare = null, mixed $value = null, ?string $type = null, ?string $named_key = null): static
    {
        return $this->where($key, $compare, $value, $type, $named_key, Relation::OR);
    }

    public function when($condition = null, ?callable $callback = null, ?callable $default = null): static
    {
        $condition = $condition instanceof Closure ? $condition($this) : $condition;

        if ($condition) {
            return $callback($this, $condition) ?? $this;
        } elseif ($default) {
            return $default($this, $condition) ?? $this;
        }

        return $this;
    }

    public function toArray(): array
    {
        $return = [];

        foreach ($this->query as $key => $value) {
            if ($value instanceof static) {
                $return[$key] = $value->toArray();
            } else {
                $return[$key] = $value;
            }
        }

        return $return;
    }

    public function empty(): bool
    {
        return empty($this->query);
    }

    public function notEmpty(): bool
    {
        return !$this->empty();
    }
}
