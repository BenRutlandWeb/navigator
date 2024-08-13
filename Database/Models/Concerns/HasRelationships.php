<?php

namespace Navigator\Database\Models\Concerns;

use Navigator\Database\BuilderInterface;
use Navigator\Database\ModelInterface;
use Navigator\Database\Models\Comment;
use Navigator\Database\Models\Post;
use Navigator\Database\Models\Term;
use Navigator\Database\Models\User;
use Navigator\Database\Query\TaxQuery;

trait HasRelationships
{
    /**
     * @template T of ModelInterface
     * @param class-string<T>|null $model
     * @return T
     */
    public function belongsTo(string $model, string|callable|null $fk = null, ?string $pk = null): ?ModelInterface
    {
        if (is_callable($fk)) {
            $fk($query = $model::query());
            return $query->first();
        }

        if ($this instanceof Post) {
            if (is_a($model, Post::class, true)) {
                return $model::find($this->post_parent);
            } elseif (is_a($model, User::class, true)) {
                return $model::find($this->post_author);
            }
        } elseif ($this instanceof Term) {
            if (is_a($model, Term::class, true)) {
                return $model::find($this->parent);
            }
        } elseif ($this instanceof Comment) {
            if (is_a($model, User::class, true)) {
                return $model::find($this->user_id);
            } elseif (is_a($model, Post::class, true)) {
                return $model::find($this->comment_post_ID);
            } elseif (is_a($model, Comment::class, true)) {
                return $model::find($this->comment_parent);
            }
        }

        return $model::query()->where($fk, $this->$pk)->first();
    }

    /**
     * @template T of ModelInterface
     * @param class-string<T> $model
     * @return BuilderInterface<T>
     */
    public function hasMany(string $model, string|callable|null $fk = null, ?string $pk = null): BuilderInterface
    {
        if (is_callable($fk)) {
            $fk($query = $model::query());
            return $query;
        }

        if ($this instanceof User) {
            if (is_a($model, Post::class, true)) {
                return $model::query()->where('post_author', $this->id());
            } elseif (is_a($model, Comment::class, true)) {
                return $model::query()->where('user_id', $this->id());
            }
        } elseif ($this instanceof Post) {
            if (is_a($model, Post::class, true)) {
                return $model::query()->where('post_parent', $this->id());
            } elseif (is_a($model, Term::class, true)) {
                return $model::query()->where('object_ids', $this->id());
            } elseif (is_a($model, Comment::class, true)) {
                return $model::query()->where('comment_post_ID', $this->id());
            }
        } elseif ($this instanceof Term) {
            if (is_a($model, Post::class, true)) {
                return $model::query()->whereTax(function (TaxQuery $query) {
                    $query->where($this->taxonomy, 'IN', $this->id());
                });
            } elseif (is_a($model, Term::class, true)) {
                return $model::query()->where('parent', $this->id());
            }
        } elseif ($this instanceof Comment) {
            if (is_a($model, Comment::class, true)) {
                return $model::query()->where('comment_parent', $this->id());
            }
        }

        return $model::query()->where($fk, $this->$pk);
    }
}
