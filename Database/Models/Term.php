<?php

namespace Navigator\Database\Models;

use Navigator\Collections\Collection;
use Navigator\Database\ModelInterface;
use Navigator\Database\Models\Concerns\HasMeta;
use Navigator\Database\Models\Concerns\HasRelationships;
use Navigator\Database\Query\TermBuilder;
use Navigator\Database\Relation;
use WP_Term;

class Term implements ModelInterface
{
    use HasRelationships;
    use HasMeta;

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

        $term = wp_insert_term($attributes['name'], $taxonomy, $attributes);

        if (!is_wp_error(dump($term))) {
            return static::find($term['term_id']);
        }

        return null;
        #wp_set_object_terms($this->attributes['object_ids'], $term['term_id'], $taxonomy, false);
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
