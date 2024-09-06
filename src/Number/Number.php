<?php

namespace Navigator\Number;

use NumberFormatter;

class Number
{
    public const PI = M_PI;

    public const TAU = M_PI * 2;

    public static function ceil(int|float $number): int
    {
        return ceil($number);
    }

    public static function clamp(int|float $number, int|float $min, int|float $max): int
    {
        return static::min(static::max($number, $min), $max);
    }

    public static function currency(int|float $number, string $in = 'USD', string $locale = 'en'): string
    {
        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($number, $in);
    }

    public static function fileSize(int|float $bytes, int $precision = 0, ?int $maxPrecision = null): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

        for ($i = 0; ($bytes / 1024) > 0.9 && ($i < count($units) - 1); $i++) {
            $bytes /= 1024;
        }

        return sprintf('%s %s', static::format($bytes, $precision, $maxPrecision), $units[$i]);
    }

    public static function floor(int|float $number): int
    {
        return floor($number);
    }

    public static function format(int|float $number, ?int $precision = null, ?int $maxPrecision = null, string $locale = 'en'): string
    {
        $formatter = new NumberFormatter($locale, NumberFormatter::DECIMAL);

        if (! is_null($maxPrecision)) {
            $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $maxPrecision);
        } elseif (! is_null($precision)) {
            $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $precision);
        }

        return $formatter->format($number);
    }

    public static function max(int|float $number): int
    {
        return max($number);
    }

    public static function min(int|float $number): int
    {
        return min($number);
    }

    public static function ordinal(int|float $number, string $locale = 'en'): string
    {
        $formatter = new NumberFormatter($locale, NumberFormatter::ORDINAL);

        return $formatter->format($number);
    }

    public static function percentage(int|float $number, int $precision = 0, ?int $maxPrecision = null, string $locale = 'en'): string
    {
        $formatter = new NumberFormatter($locale, NumberFormatter::PERCENT);

        if (!is_null($maxPrecision)) {
            $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $maxPrecision);
        } else {
            $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $precision);
        }

        return $formatter->format($number / 100);
    }

    public static function power(int|float $number, int|float $exponent = 2): int
    {
        return pow($number, $exponent);
    }

    public static function round(int|float $number): int
    {
        return round($number);
    }

    public static function spell(int|float $number, string $locale = 'en'): string
    {
        $formatter = new NumberFormatter($locale, NumberFormatter::SPELLOUT);

        return $formatter->format($number);
    }

    public static function squareRoot(int|float $number): int
    {
        return sqrt($number);
    }
}
