<?php

namespace Navigator\Str;

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;

class Pluralizer
{
    protected static ?Inflector $inflector = null;

    protected static string $language = 'english';

    public static function plural(string $value, int $count = 2): string
    {
        if ($count === 1) {
            return $value;
        }

        $plural = static::inflector()->pluralize($value);

        return static::matchCase($plural, $value);
    }

    public static function singular(string $value): string
    {
        $singular = static::inflector()->singularize($value);

        return static::matchCase($singular, $value);
    }

    protected static function matchCase(string $value, string $comparison): string
    {
        $functions = ['mb_strtolower', 'mb_strtoupper', 'ucfirst', 'ucwords'];

        foreach ($functions as $function) {
            if ($function($comparison) === $comparison) {
                return $function($value);
            }
        }

        return $value;
    }

    public static function inflector(): Inflector
    {
        if (is_null(static::$inflector)) {
            static::$inflector = InflectorFactory::createForLanguage(static::$language)->build();
        }

        return static::$inflector;
    }

    public static function useLanguage(string $language): void
    {
        static::$language = $language;

        static::$inflector = null;
    }
}
