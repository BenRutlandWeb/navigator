<?php

namespace Navigator\Database\Query;

use Closure;
use Navigator\Collections\Arr;
use Navigator\Database\Query\Concerns\Relation;
use Navigator\Foundation\Concerns\Arrayable;

class TaxQuery implements Arrayable
{
    public function __construct(protected array $query = [])
    {
        //
    }

    public static function make(array $query = []): static
    {
        return new static($query);
    }

    public function where(string|callable $taxonomy, ?string $operator = null, mixed $terms = null, ?string $field = null, bool $include_children = false, Relation $relation = Relation::AND): static
    {
        $this->query['relation'] = $relation->value;

        // value is the name of a taxonomy but also a callable so we need to
        // prevent calling it as a function and instead treat it as a string
        if (is_callable($taxonomy) && $taxonomy !== 'value') {
            $this->query[] = $subQuery = new static;

            $taxonomy($subQuery);

            return $this;
        }

        $attributes = Arr::filter(compact('taxonomy', 'operator', 'terms', 'field', 'include_children'), function ($entry) {
            return !is_null($entry);
        });

        $this->query[] = $attributes;

        return $this;
    }

    public function orWhere(string|callable $taxonomy, ?string $operator = null, mixed $terms = null, ?string $field = null, bool $include_children = false): static
    {
        return $this->where($taxonomy, $operator, $terms, $field, $include_children, Relation::OR);
    }

    public function when(mixed $condition = null, ?callable $callback = null, ?callable $default = null): static
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
