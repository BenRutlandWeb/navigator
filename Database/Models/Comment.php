<?php

namespace Navigator\Database\Models;

use Carbon\Carbon;
use Navigator\Collections\Collection;
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

    /** @return Collection<int, static> */
    public static function all(): Collection
    {
        return static::query()->get();
    }

    public function id(): int
    {
        return $this->object->comment_ID;
    }

    public function createdAt(): Carbon
    {
        return Carbon::create($this->comment_date);
    }

    public static function create(array $attributes = []): static
    {
        unset($attributes['comment_ID']);

        if ($id = wp_insert_comment($attributes)) {
            return static::find($id);
        }

        return null;
    }

    public function update(array $attributes = []): bool
    {
        $attributes['comment_ID'] = $this->id();

        return wp_update_comment($attributes, false);
    }

    public function delete(): bool
    {
        return wp_delete_comment($this->id(), true);
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
