<?php

namespace Navigator\Database\Models;

use Generator;
use Navigator\Collections\Collection;
use Navigator\Database\Exceptions\ModelNotFoundException;
use Navigator\Database\Factories\Concerns\HasFactory;
use Navigator\Database\Factories\TermFactory;
use Navigator\Database\ModelInterface;
use Navigator\Database\Models\Concerns\HasMeta;
use Navigator\Database\Models\Concerns\HasRelationships;
use Navigator\Database\Models\Concerns\InteractsWithAttributes;
use Navigator\Database\Query\TermBuilder;
use Navigator\Database\Relation;
use Navigator\Pagination\Paginator;
use WP_Term;

/**
 * @property-read int $term_id
 * @property string $name
 * @property string $slug
 * @property int $term_group
 * @property int $term_taxonomy_id
 * @property string $taxonomy
 * @property string $description
 * @property int $parent
 * @property-read int $count
 * @property string $filter
 */
class Term implements ModelInterface
{
    use HasFactory;
    use HasMeta;
    use HasRelationships;
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

    public static function findOrFail(int $id): ?static
    {
        return static::find($id) ?? throw new ModelNotFoundException(static::class);
    }

    /** @return Collection<int, static> */
    public static function all(): Collection
    {
        return static::query()->get();
    }

    /** @param (callable(Collection<int, static>, int): mixed) $callback */
    public static function chunk(int $count, callable $callback): bool
    {
        return static::query()->chunk($count, $callback);
    }

    /** @return Paginator<static> */
    public static function paginate(int $perPage = 15, string $pageName = 'page', ?int $page = null, ?int $total = null): Paginator
    {
        return static::query()->paginate($perPage, $pageName, $page, $total);
    }

    /** @return Generator<static> */
    public static function lazy(int $chunk = 1000): Generator
    {
        return static::query()->lazy($chunk);
    }

    public function id(): int
    {
        return $this->object->term_id;
    }

    /** @return ?T */
    public static function create(array $attributes): ?static
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

    public function update(array $attributes): bool
    {
        return $this->fill($attributes)->save();
    }

    public function save(): bool
    {
        $return = wp_update_term($this->id(), $this->taxonomy, $this->toArray());

        return !is_wp_error($return);
    }

    public function delete(): bool
    {
        return static::destroy($this->id()) ? true : false;
    }

    /** @param int|array<int, int> $ids */
    public static function destroy(int|array $ids): int
    {
        $taxonomy = Relation::getObjectType(static::class);

        $affectedRows = 0;

        foreach ((array) $ids as $id) {
            $deleted = wp_delete_term($id, $taxonomy);
            if ($deleted && !is_wp_error($deleted)) {
                $affectedRows++;
            }
        }

        return $affectedRows;
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

    public static function slug(): string
    {
        return Relation::getObjectType(static::class);
    }

    public static function factory(): TermFactory
    {
        return new TermFactory(static::class);
    }
}
