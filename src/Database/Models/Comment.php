<?php

namespace Navigator\Database\Models;

use Carbon\Carbon;
use Generator;
use Navigator\Collections\Collection;
use Navigator\Database\Exceptions\ModelNotFoundException;
use Navigator\Database\ModelInterface;
use Navigator\Database\Models\Concerns\HasMeta;
use Navigator\Database\Models\Concerns\HasRelationships;
use Navigator\Database\Models\Concerns\InteractsWithAttributes;
use Navigator\Database\Query\CommentBuilder;
use WP_Comment;

class Comment implements ModelInterface
{
    use HasRelationships;
    use HasMeta;
    use InteractsWithAttributes;

    public function __construct(readonly public WP_Comment $object)
    {
        //
    }

    /** @return CommentBuilder<static> */
    public static function query(): CommentBuilder
    {
        $query = new CommentBuilder(static::class);

        static::withGlobalScopes($query);

        return $query;
    }

    public static function withGlobalScopes(CommentBuilder $query): void
    {
        //
    }

    public static function find(int $id): ?static
    {
        if ($comment = WP_Comment::get_instance($id)) {
            return new static($comment);
        }

        return null;
    }

    public static function findOrFail(int $id): ?static
    {
        return static::find($id) ?? throw new ModelNotFoundException(static::class);
    }

    /** @return Collection<int, static> */
    public static function all(): Collection
    {
        return static::query()->get();
    }

    /** @param (callable(T, int): mixed) $callback */
    public static function chunk(int $count, callable $callback): bool
    {
        return static::query()->chunk($count, $callback);
    }

    public static function lazy(int $chunk = 1000): Generator
    {
        return static::query()->lazy($chunk);
    }

    public function id(): int
    {
        return $this->object->comment_ID;
    }

    public function createdAt(): Carbon
    {
        return Carbon::create($this->comment_date);
    }

    public static function create(array $attributes): static
    {
        unset($attributes['comment_ID']);

        if ($id = wp_insert_comment($attributes)) {
            return static::find($id);
        }

        return null;
    }

    public function update(array $attributes): bool
    {
        $attributes['comment_ID'] = $this->id();

        return $this->fill($attributes)->save();
    }

    public function save(): bool
    {
        return wp_update_comment($this->toArray(), false) ? true : false;
    }

    public function delete(): bool
    {
        return static::destroy($this->id()) ? true : false;
    }

    /** @param int|array<int, int> $ids */
    public static function destroy(int|array $ids): int
    {
        $affectedRows = 0;

        foreach ((array) $ids as $id) {
            if (wp_delete_comment($id, true)) {
                $affectedRows++;
            }
        }

        return $affectedRows;
    }

    public function associate(ModelInterface $model): void
    {
        if ($model instanceof User) {
            $this->update(['user_id' => $model->id()]);
        } elseif ($model instanceof Post) {
            $this->update(['comment_post_ID' => $model->id()]);
        } elseif ($model instanceof static) {
            $this->update(['comment_parent' => $model->id()]);
        }
    }

    public function disassociate(ModelInterface $model): void
    {
        if ($model instanceof User) {
            $this->update(['user_id' => 0]);
        } elseif ($model instanceof Post) {
            $this->update(['comment_post_ID' => 0]);
        } elseif ($model instanceof static) {
            $this->update(['comment_parent' => 0]);
        }
    }
}
