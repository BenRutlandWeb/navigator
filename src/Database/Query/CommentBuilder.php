<?php

namespace Navigator\Database\Query;

use Navigator\Collections\Collection;
use Navigator\Database\BuilderInterface;
use Navigator\Database\Exceptions\ModelNotFoundException;
use Navigator\Database\ModelInterface;
use Navigator\Database\Models\Comment;
use Navigator\Database\Query\Concerns\AggregatesQueries;
use Navigator\Database\Query\Concerns\HasAttributes;
use Navigator\Database\Query\Concerns\Order;
use Navigator\Database\Query\Concerns\PaginatesQueries;
use Navigator\Database\Query\Concerns\QueriesDates;
use Navigator\Database\Query\Concerns\QueriesMeta;
use WP_Comment;
use WP_Comment_Query;

/** @template T of ModelInterface */
class CommentBuilder implements BuilderInterface
{
    use AggregatesQueries;
    use HasAttributes;
    /** @use PaginatesQueries<T> */
    use PaginatesQueries;
    use QueriesDates;
    use QueriesMeta;

    /** @param class-string<T> $model */
    public function __construct(readonly public string $model, protected Attributes $attributes = new CommentAttributes())
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

    /** @return ?T */
    public function firstOrFail(): ?Comment
    {
        return $this->first() ?? throw new ModelNotFoundException($this->model);
    }

    public function count(): int
    {
        return $this->where('count', true)->runQuery()->get_comments();
    }

    public function toSql(): ?string
    {
        return $this->runQuery()->request;
    }

    public function runQuery(): WP_Comment_Query
    {
        return new WP_Comment_Query($this->attributes->forQuery());
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

        foreach ($this->get() as $comment) {
            if ($comment->update($attributes)) {
                $affectedRows++;
            }
        }

        return $affectedRows;
    }

    public function delete(): int
    {
        $affectedRows = 0;

        foreach ($this->get() as $comment) {
            if ($comment->delete()) {
                $affectedRows++;
            }
        }

        return $affectedRows;
    }
}
