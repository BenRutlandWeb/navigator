<?php

namespace Navigator\Database\Query;

use Navigator\Collections\Collection;
use Navigator\Database\BuilderInterface;
use Navigator\Database\ModelInterface;
use Navigator\Database\Models\Comment;
use Navigator\Database\Query\Concerns\Order;
use Navigator\Database\Query\Concerns\QueriesDates;
use Navigator\Database\Query\Concerns\QueriesMeta;
use Navigator\Pagination\Paginator;
use WP_Comment;
use WP_Comment_Query;

/** @template T of ModelInterface */
class CommentBuilder implements BuilderInterface
{
    use QueriesDates;
    use QueriesMeta;

    protected array $attributes = [];

    /** @param class-string<T> $model */
    public function __construct(readonly public string $model)
    {
        //
    }

    public function where(string $key, mixed $value): static
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    public function whereIn(string $key, array $values): static
    {
        return $this->where($key, $values);
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
        return $this->where('search', $query);
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
        return $this->orderBy('comment_date_gmt', Order::DESC);
    }

    public function oldest(): static
    {
        return $this->orderBy('comment_date_gmt');
    }

    /** @return Collection<int ,T> */
    public function get(): Collection
    {
        return Collection::make($this->runQuery()->get_comments())
            ->map(function (WP_Comment $comment) {
                $model = $this->model;
                return new $model($comment);
            });
    }

    /** @return ?T */
    public function first(): ?Comment
    {
        return $this->limit(1)->get()->first();
    }

    public function count(): int
    {
        return $this->where('count', true)->runQuery()->get_comments();
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

    public function runQuery(): WP_Comment_Query
    {
        return new WP_Comment_Query($this->attributes);
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
        $id = wp_insert_comment($attributes);

        if ($id) {
            return $this->model::find($id);
        }

        return null;
    }
}
