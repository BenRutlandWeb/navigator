<?php

namespace Navigator\Database;

use Navigator\Collections\Arr;

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
        static::$morphMap = $map;
    }

    /** @return class-string<ModelInterface>|null */
    public static function getMorphedModel(string $alias): ?string
    {
        return static::$morphMap[$alias] ?? null;
    }

    /** @param class-string<ModelInterface> $model */
    public static function getObjectType(string $model): ?string
    {
        return Arr::flip(static::$morphMap)[$model] ?? null;
    }
}
