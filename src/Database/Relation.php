<?php

namespace Navigator\Database;

use Navigator\Collections\Arr;
use Navigator\Str\Str;

class Relation
{
    /**
     * @var array<string,class-string<ModelInterface>>
     */
    protected static array $morphMap = [];

    /**
     * @param array<string,class-string<ModelInterface>> $map
     */
    public static function enforceMorphMap(array $map = []): void
    {
        foreach ($map as $alias => $model) {
            static::addMorphedModel($alias, $model);
        }
    }

    /** @param class-string<ModelInterface> $model */
    public static function addMorphedModel(string $alias, string $model): void
    {
        static::$morphMap[$alias] = $model;
    }

    /** @return class-string<ModelInterface>|null */
    public static function getMorphedModel(string $alias): ?string
    {
        return static::$morphMap[$alias] ?? '\\App\\Models\\' . Str::studly($alias);
    }

    /** @param class-string<ModelInterface> $model */
    public static function getObjectType(string $model): ?string
    {
        return Arr::flip(static::$morphMap)[$model] ?? Str::snake(basename($model));
    }
}
