<?php

namespace Navigator\Database\Query;

use Navigator\Collections\Collection;
use Navigator\Database\BuilderInterface;
use Navigator\Database\ModelInterface;
use Navigator\Database\Models\User;
use Navigator\Database\Query\Concerns\HasAttributes;
use Navigator\Database\Query\Concerns\Order;
use Navigator\Database\Query\Concerns\QueriesMeta;
use Navigator\Pagination\Paginator;
use WP_User;
use WP_User_Query;

/** @template T of ModelInterface */
class UserBuilder implements BuilderInterface
{
    use HasAttributes;
    use QueriesMeta;

    /** @param class-string<T> $model */
    public function __construct(readonly public string $model, protected Attributes $attributes = new UserAttributes())
    {
        //
    }

    public function include(array $ids): static
    {
        return $this->where('include', $ids);
    }

    public function exclude(array $ids): static
    {
        return $this->where('exclude', $ids);
    }

    public function search(string $query): static
    {
        return $this->where('search', $query)->whereIn('search_columns', [
            'ID', 'user_login', 'user_nicename', 'user_email', 'user_url',
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

    public function role(string $role): static
    {
        return $this->where('role', $role);
    }

    /** @param string[] $roles */
    public function roleIn(array $roles): static
    {
        return $this->where('role__in', $roles);
    }

    /** @param string[] $roles */
    public function roleNotIn(array $roles): static
    {
        return $this->where('role__not_in', $roles);
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

    public function count(): int
    {
        return $this->where('count_total', true)->runQuery()->get_total();
    }

    public function toSql(): ?string
    {
        return $this->runQuery()->request;
    }

    public function delete(): bool
    {
        foreach ($this->get() as $post) {
            $post->delete();
        }

        return true;
    }

    public function runQuery(): WP_User_Query
    {
        return new WP_User_Query($this->attributes->forQuery());
    }

    /** @return Paginator<T> */
    public function paginate(int $perPage = 5, string $pageName = 'page', ?int $page = null, ?int $total = null): Paginator
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);
        $query = clone $this;
        $results = $this->limit($perPage)->offset(($page - 1) * $perPage)->get();

        return new Paginator($results, $total ?? $query->count(), $perPage, $page, $pageName);
    }

    /** @return ?T */
    public function create(array $attributes = []): ?ModelInterface
    {
        return $this->model::create(
            $this->attributes->merge($attributes)->toArray()
        );
    }
}
