<?php

namespace Navigator\Database\Models;

use Navigator\Collections\Collection;
use Navigator\Database\ModelInterface;
use Navigator\Database\Models\Concerns\HasMeta;
use Navigator\Database\Models\Concerns\HasRelationships;
use Navigator\Database\Models\Concerns\InteractsWithAttributes;
use Navigator\Database\Query\TermBuilder;
use Navigator\Database\Relation;
use WP_Term;

class Term implements ModelInterface
{
    use HasRelationships;
    use HasMeta;
    use InteractsWithAttributes;

    public function __construct(readonly public WP_Term $object)
    {
        //
    }

    /** @return TermBuilder<static> */
    public static function query(): TermBuilder
    {
        $query = new TermBuilder(static::class);

        $query->where('taxonomy', Relation::getObjectType(static::class))
            ->where('hide_empty', false);

        static::withGlobalScopes($query);

        return $query;
    }

    public static function withGlobalScopes(TermBuilder $query): void
    {
        //
    }

    public static function find(int $id): ?static
    {
        $taxonomy = Relation::getObjectType(static::class);

        if ($term = WP_Term::get_instance($id, $taxonomy)) {
            return new static($term);
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
        return $this->object->term_id;
    }

    /** @return ?T */
    public static function create(array $attributes = []): ?static
    {
        $taxonomy = Relation::getObjectType(static::class);

        unset($attributes['term_id']);

        $term = wp_insert_term($attributes['name'], $taxonomy, $attributes);

        if (!is_wp_error($term)) {
            $term = static::find($term['term_id']);

            if ($term && isset($attributes['object_ids'])) {
                wp_add_object_terms($attributes['object_ids'], $term->id(), $term->taxonomy());
            }

            return $term;
        }

        return null;
    }

    public function update(array $attributes = []): bool
    {
        $return = wp_update_term($this->id(), $this->taxonomy, $attributes);

        return is_wp_error($return) ? false : true;
    }

    public function delete(): bool
    {
        $return = wp_delete_term($this->id(), $this->taxonomy);

        return is_wp_error($return) ? false : true;
    }

    public function associate(ModelInterface $model): void
    {
        if ($model instanceof static) {
            $this->update(['parent' => $model->id()]);
        }
    }

    public function disassociate(ModelInterface $model): void
    {
        if ($model instanceof static) {
            $this->update(['parent' => 0]);
        }
    }

    /** @param Collection<int, Post>|array<int, Post>|Post $posts */
    public function attach(Collection|array|Post $posts): void
    {
        foreach ((is_iterable($posts) ? $posts : [$posts]) as $post) {
            wp_add_object_terms($post->id(), $this->id(), $this->taxonomy());
        }
    }

    /** @param Collection<int, Post>|array<int, Post>|Post $posts */
    public function detach(Collection|array|Post $posts): void
    {
        foreach ((is_iterable($posts) ? $posts : [$posts]) as $post) {
            wp_remove_object_terms($post->id(), $this->id(), $this->taxonomy());
        }
    }

    /** @param Collection<int, Post>|array<int, Post>|Post $posts */
    public function sync(Collection|array|Post $posts): void
    {
        foreach ((is_iterable($posts) ? $posts : [$posts]) as $post) {
            wp_set_object_terms($post->id(), $this->id(), $this->taxonomy());
        }
    }

    public function taxonomy(): string
    {
        return $this->object->taxonomy;
    }
}
