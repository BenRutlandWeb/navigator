<?php

namespace Navigator\Database\Models;

use Carbon\Carbon;
use Closure;
use Generator;
use Navigator\Collections\Arr;
use Navigator\Collections\Collection;
use Navigator\Database\Exceptions\ModelNotFoundException;
use Navigator\Database\Factories\PostFactory;
use Navigator\Database\ModelInterface;
use Navigator\Database\Models\Concerns\HasMeta;
use Navigator\Database\Models\Concerns\HasPostStatus;
use Navigator\Database\Models\Concerns\HasRelationships;
use Navigator\Database\Models\Concerns\InteractsWithAttributes;
use Navigator\Database\Query\PostBuilder;
use Navigator\Database\Relation;
use Navigator\Pagination\Paginator;
use Navigator\WordPress\Concerns\Dashicon;
use stdClass;
use WP_Post;

/**
 * @property-read int $ID
 * @property int $post_author
 * @property string $post_date
 * @property string $post_date_gmt
 * @property string $post_content
 * @property string $post_title
 * @property string $post_excerpt
 * @property string $post_status
 * @property string $comment_status
 * @property string $ping_status
 * @property string $post_password
 * @property string $post_name
 * @property string $to_ping
 * @property string $pinged
 * @property string $post_modified
 * @property string $post_modified_gmt
 * @property string $post_content_filtered
 * @property int $post_parent
 * @property string $guid
 * @property int $menu_order
 * @property string $post_type
 * @property string $post_mime_type
 * @property string $comment_count
 */
class Post implements ModelInterface
{
    use HasMeta;
    use HasPostStatus;
    use HasRelationships;
    use InteractsWithAttributes;

    public function __construct(readonly public WP_Post $object = new WP_Post(new stdClass))
    {
        $this->object->post_type = Relation::getObjectType(static::class);
    }

    /** @return PostBuilder<static> */
    public static function query(): PostBuilder
    {
        $query = new PostBuilder(static::class);

        $query->where('post_type', Relation::getObjectType(static::class))
            ->limit(-1);

        static::withGlobalScopes($query);

        return $query;
    }

    public static function withGlobalScopes(PostBuilder $query): void
    {
        //
    }

    public static function find(int $id): ?static
    {
        return static::query()->include([$id])->first();
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
        return $this->object->ID;
    }

    public function createdAt(): Carbon
    {
        return Carbon::create($this->post_date);
    }

    public function updatedAt(): Carbon
    {
        return Carbon::create($this->post_modified);
    }

    public static function create(array $attributes): ?static
    {
        $attributes['post_type'] = Relation::getObjectType(static::class);

        unset($attributes['ID']);

        $model = (new static)->fill($attributes);

        return $model->save() ? $model : null;
    }

    public function update(array $attributes): bool
    {
        $attributes['ID'] = $this->id();

        return $this->fill($attributes)->save();
    }

    public function save(): bool
    {
        $id = wp_insert_post($this->toArray(), true, true);

        if (!is_wp_error($id)) {
            $this->fill(static::find($id)->toArray());

            return true;
        }

        return false;
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
            if (wp_delete_post($id, true)) {
                $affectedRows++;
            }
        }

        return $affectedRows;
    }

    public function associate(ModelInterface $model): static
    {
        if ($model instanceof User) {
            $this->update(['post_author' => $model->id()]);
        } elseif ($model instanceof static) {
            $this->update(['post_parent' => $model->id()]);
        }

        return $this;
    }

    public function disassociate(ModelInterface $model): static
    {
        if ($model instanceof User) {
            $this->update(['post_author' => 0]);
        } elseif ($model instanceof static) {
            $this->update(['post_parent' => 0]);
        }

        return $this;
    }

    /** @param Collection<int, Term>|array<int, Term>|Term $terms */
    public function attach(Collection|array|Term $terms): void
    {
        $this->setTermRelation($terms, function (array $ids, string $taxonomy): void {
            wp_add_object_terms($this->id(), $ids, $taxonomy);
        });
    }

    /** @param Collection<int, Term>|array<int, Term>|Term $terms */
    public function detach(Collection|array|Term $terms): void
    {
        $this->setTermRelation($terms, function (array $ids, string $taxonomy): void {
            wp_remove_object_terms($this->id(), $ids, $taxonomy);
        });
    }

    /** @param Collection<int, Term>|array<int, Term>|Term $terms */
    public function sync(Collection|array|Term $terms): void
    {
        $this->setTermRelation($terms, function (array $ids, string $taxonomy): void {
            wp_set_object_terms($this->id(), $ids, $taxonomy);
        });
    }

    /**
     * @param Collection<int, Term>|array<int, Term>|Term $terms
     * @param (Closure(array<int, int>, string): void) $terms
     */
    protected function setTermRelation(Collection|array|Term $terms, Closure $callback): void
    {
        Collection::make(is_iterable($terms) ? $terms : [$terms])
            ->groupBy('taxonomy')
            ->each(fn(Collection $group, string $taxonomy) => $callback(
                $group->pluck('term_id')->toArray(),
                $taxonomy
            ));
    }

    public static function dashicon(): Dashicon
    {
        return Dashicon::ADMIN_POST;
    }

    public static function slug(): string
    {
        return Relation::getObjectType(static::class);
    }

    /** @return PostFactory<static> */
    public static function factory(): PostFactory
    {
        return new PostFactory(static::class);
    }
}
