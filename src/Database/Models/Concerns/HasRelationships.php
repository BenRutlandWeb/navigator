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
     * @param (callable(BuilderInstance<T>): void)|null $callback
     * @return ?T
     */
    public function belongsTo(string $model, ?callable $callback = null): ?ModelInterface
    {
        if (is_callable($callback)) {
            $callback($query = $model::query());

            return $query->first();
        }

        if ($this instanceof Post) {
            if (is_a($model, Post::class, true)) {
                $fk = 'post_parent';
            } elseif (is_a($model, User::class, true)) {
                $fk = 'post_author';
            }
        } elseif ($this instanceof Term) {
            if (is_a($model, Term::class, true)) {
                $fk = 'parent';
            }
        } elseif ($this instanceof Comment) {
            if (is_a($model, User::class, true)) {
                $fk = 'user_id';
            } elseif (is_a($model, Post::class, true)) {
                $fk = 'comment_post_ID';
            } elseif (is_a($model, Comment::class, true)) {
                $fk = 'comment_parent';
            }
        }

        return $fk ? $model::find($this->$fk) : null;
    }

    /**
     * @template T of ModelInterface
     * @param class-string<T> $model
     * @param (callable(BuilderInstance<T>): void)|null $callback
     * @return BuilderInterface<T>
     */
    public function hasMany(string $model, ?callable $callback = null): BuilderInterface
    {
        if (is_callable($callback)) {
            $callback($query = $model::query());

            return $query;
        }

        if ($this instanceof User) {
            if (is_a($model, Post::class, true)) {
                $fk = 'post_author';
            } elseif (is_a($model, Comment::class, true)) {
                $fk = 'user_id';
            }
        } elseif ($this instanceof Post) {
            if (is_a($model, Post::class, true)) {
                $fk = 'post_parent';
            } elseif (is_a($model, Term::class, true)) {
                $fk = 'object_ids';
            } elseif (is_a($model, Comment::class, true)) {
                $fk = 'comment_post_ID';
            }
        } elseif ($this instanceof Term) {
            if (is_a($model, Post::class, true)) {
                // this is a special case so we return early
                return $model::query()->whereTax(function (TaxQuery $query) {
                    $query->where($this->taxonomy, 'IN', $this->id());
                });
            } elseif (is_a($model, Term::class, true)) {
                $fk = 'parent';
            }
        } elseif ($this instanceof Comment) {
            if (is_a($model, Comment::class, true)) {
                $fk = 'comment_parent';
            }
        }

        if ($fk) {
            return $model::query()->where($fk, $this->id());
        }

        return $model::query();
    }
}
