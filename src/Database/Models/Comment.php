<?php

namespace Navigator\Database\Models;

use Carbon\Carbon;
use Generator;
use Navigator\Collections\Collection;
use Navigator\Database\Exceptions\ModelNotFoundException;
use Navigator\Database\Factories\CommentFactory;
use Navigator\Database\ModelInterface;
use Navigator\Database\Models\Concerns\HasMeta;
use Navigator\Database\Models\Concerns\HasRelationships;
use Navigator\Database\Models\Concerns\InteractsWithAttributes;
use Navigator\Database\Query\CommentBuilder;
use Navigator\Pagination\Paginator;
use stdClass;
use WP_Comment;

/**
 * @property-read int $comment_ID
 * @property int $comment_post_ID
 * @property string $comment_author
 * @property string $comment_author_email
 * @property string $comment_author_url
 * @property string $comment_author_IP
 * @property string $comment_date
 * @property string $comment_date_gmt
 * @property string $comment_content
 * @property string $comment_karma
 * @property string $comment_approved
 * @property string $comment_agent
 * @property string $comment_type
 * @property int $comment_parent
 * @property int $user_id
 */
class Comment implements ModelInterface
{
    use HasMeta;
    use HasRelationships;
    use InteractsWithAttributes;

    public function __construct(readonly public WP_Comment $object = new WP_Comment(new stdClass))
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
        return $this->object->comment_ID;
    }

    public function createdAt(): Carbon
    {
        return Carbon::create($this->comment_date);
    }

    public static function create(array $attributes): static
    {
        unset($attributes['comment_ID']);

        $model = (new static)->fill($attributes);

        return $model->save() ? $model : null;
    }

    public function update(array $attributes): bool
    {
        $attributes['comment_ID'] = $this->id();

        return $this->fill($attributes)->save();
    }

    public function save(): bool
    {
        if ($this->object->comment_ID) {
            return !is_wp_error(wp_update_comment($this->toArray()));
        } else {
            if ($id = wp_insert_comment($this->toArray())) {
                $this->fill(static::find($id)->toArray());

                return true;
            }
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

    /** @return CommentFactory<static> */
    public static function factory(): CommentFactory
    {
        return new CommentFactory(static::class);
    }
}
