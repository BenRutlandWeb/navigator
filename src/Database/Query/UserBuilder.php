<?php

namespace Navigator\Database\Query;

use Navigator\Collections\Collection;
use Navigator\Database\BuilderInterface;
use Navigator\Database\Exceptions\ModelNotFoundException;
use Navigator\Database\ModelInterface;
use Navigator\Database\Models\User;
use Navigator\Database\Query\Concerns\AggregatesQueries;
use Navigator\Database\Query\Concerns\HasAttributes;
use Navigator\Database\Query\Concerns\Order;
use Navigator\Database\Query\Concerns\PaginatesQueries;
use Navigator\Database\Query\Concerns\QueriesMeta;
use WP_User;
use WP_User_Query;

/** @template T of ModelInterface */
class UserBuilder implements BuilderInterface
{
    use AggregatesQueries;
    use HasAttributes;
    /** @use PaginatesQueries<T> */
    use PaginatesQueries;
    use QueriesMeta;

    /** @param class-string<T> $model */
    public function __construct(readonly public string $model, protected Attributes $attributes = new UserAttributes())
    {
        //
    }

    public function include(array $ids): static
    {
        return $this->whereIn('include', $ids);
    }

    public function exclude(array $ids): static
    {
        return $this->whereIn('exclude', $ids);
    }

    public function search(string $query): static
    {
        return $this->where('search', $query)->whereIn('search_columns', [
            'ID',
            'user_login',
            'user_nicename',
            'user_email',
            'user_url',
        ]);
    }

    public function limit(int $limit): static
    {
        return $this->where('number', $limit);
    }

    public function offset(int $offset): static
    {
        return $this->where('offset', $offset);
    }

    public function orderBy(string $column, Order $direction = Order::ASC): static
    {
        return $this->where('orderby', $column)->where('order', $direction->value);
    }

    public function latest(): static
    {
        return $this->orderBy('registered', Order::DESC);
    }

    public function oldest(): static
    {
        return $this->orderBy('registered');
    }

    public function email(string $email): static
    {
        return $this->where('search', $email)->whereIn('search_columns', [
            'user_email',
        ]);
    }

    public function role(string $role): static
    {
        return $this->where('role', $role);
    }

    /** @param string[] $roles */
    public function roleIn(array $roles): static
    {
        return $this->whereIn('role__in', $roles);
    }

    /** @param string[] $roles */
    public function roleNotIn(array $roles): static
    {
        return $this->whereIn('role__not_in', $roles);
    }

    /** @return Collection<int, T> */
    public function get(): Collection
    {
        return Collection::make($this->runQuery()->get_results() ?? [])
            ->map(function (WP_User $user) {
                $model = $this->model;
                return new $model($user);
            });
    }

    /** @return ?T */
    public function first(): ?User
    {
        return $this->limit(1)->get()->first();
    }

    /** @return ?T */
    public function firstOrFail(): ?User
    {
        return $this->first() ?? throw new ModelNotFoundException($this->model);
    }

    public function count(): int
    {
        return $this->where('count_total', true)->runQuery()->get_total();
    }

    public function toSql(): ?string
    {
        return $this->runQuery()->request;
    }

    public function runQuery(): WP_User_Query
    {
        return new WP_User_Query($this->attributes->forQuery());
    }

    /** @return ?T */
    public function create(array $attributes): ?ModelInterface
    {
        return $this->model::create(
            $this->attributes->merge($attributes)->toArray()
        );
    }

    public function update(array $attributes): int
    {
        $affectedRows = 0;

        foreach ($this->get() as $user) {
            if ($user->update($attributes)) {
                $affectedRows++;
            }
        }

        return $affectedRows;
    }

    public function delete(): int
    {
        $affectedRows = 0;

        foreach ($this->get() as $user) {
            if ($user->delete()) {
                $affectedRows++;
            }
        }

        return $affectedRows;
    }
}
