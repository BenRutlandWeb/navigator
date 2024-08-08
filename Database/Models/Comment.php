<?php

namespace Navigator\Database\Models;

use Navigator\Collections\Collection;
use Navigator\Database\ModelInterface;
use Navigator\Database\Models\Concerns\HasMeta;
use Navigator\Database\Models\Concerns\HasRelationships;
use Navigator\Database\Query\CommentBuilder;
use WP_Comment;

class Comment implements ModelInterface
{
    use HasRelationships;
    use HasMeta;

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

    public static function create(array $attributes = []): static
    {
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

    public function __isset(string $key): bool
    {
        return isset($this->object->$key);
    }

    public function __get(string $key): mixed
    {
        return $this->object->$key;
    }

    public function toArray(): array
    {
        return $this->object->to_array();
    }

    public function jsonSerialize(): array
    {
        return $this->object->to_array();
    }
}
