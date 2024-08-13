<?php

namespace Navigator\Database\Query;

use JsonSerializable;
use Navigator\Collections\Collection;
use Navigator\Database\BuilderInterface;
use Navigator\Database\ModelInterface;
use Navigator\Database\Models\Post;
use Navigator\Database\Query\Concerns\HasAttributes;
use Navigator\Database\Query\Concerns\Order;
use Navigator\Database\Query\Concerns\QueriesDates;
use Navigator\Database\Query\Concerns\QueriesMeta;
use Navigator\Database\Query\Concerns\QueriesTax;
use Navigator\Database\Relation;
use Navigator\Foundation\Concerns\Arrayable;
use Navigator\Pagination\Paginator;
use WP_Post;
use WP_Query;

/** @template T of ModelInterface */
class PostBuilder implements Arrayable, BuilderInterface, JsonSerializable
{
    use HasAttributes;
    use QueriesDates;
    use QueriesMeta;
    use QueriesTax;

    /** @param class-string<T> $model */
    public function __construct(readonly public string $model, protected Attributes $attributes = new PostAttributes())
    {
        //
    }

    public function include(array $ids): static
    {
        return $this->where('post__in', $ids);
    }

    public function exclude(array $ids): static
    {
        return $this->where('post__not_in', $ids);
    }

    public function search(string $query): static
    {
        return $this->where('s', $query);
    }

    public function limit(int $limit): static
    {
        return $this->where('posts_per_page', $limit);
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
        return $this->orderBy('date', Order::DESC);
    }

    public function oldest(): static
    {
        return $this->orderBy('date');
    }

    public function author(int $id): static
    {
        return $this->where('author', $id);
    }

    /** @param int[] $ids */
    public function authorIn(array $ids): static
    {
        return $this->where('author__in', $ids);
    }

    /** @param int[] $ids */
    public function authorNotIn(array $ids): static
    {
        return $this->where('author__not_in', $ids);
    }

    public function parent(int $id): static
    {
        return $this->where('post_parent', $id);
    }

    /** @param int[] $ids */
    public function parentIn(array $ids): static
    {
        return $this->where('post_parent__in', $ids);
    }

    /** @param int[] $ids */
    public function parentNotIn(array $ids): static
    {
        return $this->where('post_parent__not_in', $ids);
    }

    public function status(string|array $status): static
    {
        return $this->where('post_status', $status);
    }

    public function postFormat(string|array $format): static
    {
        return $this->whereTax(fn (TaxQuery $q) => $q->where('post_format', 'IN', $format));
    }

    /** @return Collection<int ,T> */
    public function get(): Collection
    {
        return Collection::make($this->runQuery()->posts)
            ->map(function (WP_Post $post) {
                $model = Relation::getMorphedModel($post->post_type) ?? $this->model;

                return new $model($post);
            });
    }

    /** @return ?T */
    public function first(): ?Post
    {
        return $this->limit(1)->get()->first();
    }

    public function count(): int
    {
        return $this->limit(-1)->runQuery()->post_count;
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

    public function runQuery(): WP_Query
    {
        return new WP_Query($this->attributes->forQuery());
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
