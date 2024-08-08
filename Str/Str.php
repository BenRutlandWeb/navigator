<?php

namespace Navigator\Str;

use Carbon\Carbon;
use Navigator\Collections\Arr;
use Throwable;

class Str
{
    const UTF_8 = 'UTF-8';

    const ELLIPSIS = '…';

    public static function of(string $string): Stringable
    {
        return new Stringable($string);
    }

    public static function after(string $subject, string $search): string
    {
        return $search === '' ? $subject : static::of($subject)->explode($search, 2)->reverse()->first();
    }

    public static function afterLast(string $subject, string $search): string
    {
        if ($search === '') {
            return $subject;
        }

        $position = mb_strrpos($subject, (string) $search);

        if ($position === false) {
            return $subject;
        }

        return static::substr($subject, $position + static::length($search));
    }

    public static function basename(string $string, string $suffix = ''): string
    {
        return basename($string, $suffix);
    }

    public static function before(string $subject, string $search): string
    {
        if ($search === '') {
            return $subject;
        }

        $result = mb_strstr($subject, (string) $search, true);

        return $result === false ? $subject : $result;
    }

    public static function beforeLast(string $subject, string $search): string
    {
        if ($search === '') {
            return $subject;
        }

        $pos = mb_strrpos($subject, $search);

        if ($pos === false) {
            return $subject;
        }

        return static::substr($subject, 0, $pos);
    }

    public static function between(string $subject, string $from, string $to): string
    {
        if ($from === '' || $to === '') {
            return $subject;
        }

        return static::beforeLast(static::after($subject, $from), $to);
    }

    public static function betweenFirst(string $subject, string $from, string $to): string
    {
        if ($from === '' || $to === '') {
            return $subject;
        }

        return static::before(static::after($subject, $from), $to);
    }

    public static function camel(string $string): string
    {
        return static::of($string)->studly()->lcfirst();
    }

    public static function charAt(string $subject, int $index): string
    {
        $length = static::length($subject);

        if ($index < 0 ? $index < -$length : $index > $length - 1) {
            return false;
        }

        return static::substr($subject, $index, 1);
    }

    public static function classBasename(object|string $class): string
    {
        $class = is_object($class) ? get_class($class) : $class;

        return static::basename(static::replace('\\', '/', $class));
    }

    public static function contains(string $haystack, array|string $needles, bool $ignoreCase = false): bool
    {
        if ($ignoreCase) {
            $haystack = static::lower($haystack);
        }

        if (!is_iterable($needles)) {
            $needles = (array) $needles;
        }

        foreach ($needles as $needle) {
            if ($ignoreCase) {
                $needle = static::lower($needle);
            }

            if ($needle !== '' && str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    public static function containsAll(string $haystack, array|string $needles, bool $ignoreCase = false): bool
    {
        foreach ($needles as $needle) {
            if (!static::contains($haystack, $needle, $ignoreCase)) {
                return false;
            }
        }

        return true;
    }

    public static function convertCase(string $string, int $mode = MB_CASE_FOLD, ?string $encoding = Str::UTF_8): string
    {
        return mb_convert_case($string, $mode, $encoding);
    }

    public static function dirname(string $string, int $levels = 1): string
    {
        return dirname($string, $levels);
    }

    public static function endsWith(string $haystack, array|string $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle !== '' && str_ends_with($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    /** @return array<int, string> */
    public static function explode(string $string, string $separator = ' ', int $limit = PHP_INT_MAX): array
    {
        return explode($separator, $string, $limit);
    }

    public static function exactly(string $string, string $value): bool
    {
        return $string === $value;
    }

    public static function finish(string $value, string $cap): string
    {
        $quoted = preg_quote($cap, '/');

        return preg_replace('/(?:' . $quoted . ')+$/u', '', $value) . $cap;
    }

    public static function fromBase64(string $string, bool $strict = false): string|false
    {
        return base64_decode($string, $strict);
    }

    public static function headline(string $value): string
    {
        $parts = static::explode($value, ' ');

        $parts = count($parts) > 1
            ? Arr::map($parts, [static::class, 'title'])
            : Arr::map(static::ucsplit(Arr::join($parts, '_')), [static::class, 'title']);

        $collapsed = static::replace(['-', '_', ' '], '_', Arr::join($parts, '_'));

        return Arr::join(Arr::filter(static::explode($collapsed, '_')), ' ');
    }

    /** @param string|array<int, string> $pattern */
    public static function is(string|array $pattern, string $value): bool
    {
        $pattern = (array) $pattern;

        foreach ($pattern as $pattern) {
            $pattern = (string) $pattern;

            // If the given value is an exact match we can of course return true right
            // from the beginning. Otherwise, we will translate asterisks and do an
            // actual pattern match against the two strings to see if they match.
            if ($pattern === $value) {
                return true;
            }

            $pattern = preg_quote($pattern, '#');

            // Asterisks are translated into zero-or-more regular expression wildcards
            // to make it convenient to check if the strings starts with the given
            // pattern such as "library/*", making any string check convenient.
            $pattern = static::replace('\*', '.*', $pattern);

            if (preg_match('#^' . $pattern . '\z#u', $value) === 1) {
                return true;
            }
        }

        return false;
    }

    public static function isEmpty(string $string): bool
    {
        return $string === '';
    }

    public static function isNotEmpty(string $string): bool
    {
        return !static::isEmpty($string);
    }

    public static function isJson(mixed $string): bool
    {
        return is_string($string) ? json_validate($string) : false;
    }

    public static function isMatch(string|array $pattern, string $value): bool
    {
        $pattern =  (array) $pattern;

        foreach ($pattern as $pattern) {
            $pattern = (string) $pattern;

            if (preg_match($pattern, $value) === 1) {
                return true;
            }
        }

        return false;
    }

    public static function isUuid(string $string): bool
    {
        return wp_is_uuid($string);
    }

    public static function isUrl(string $string): bool
    {
        return esc_url_raw($string) === $string;
    }

    public static function kebab(string $string,): string
    {
        return static::snake($string, '-');
    }

    public static function lcfirst(string $string, ?string $encoding = Str::UTF_8): string
    {
        return mb_lcfirst($string, $encoding);
    }

    public static function length(string $string): int
    {
        return mb_strlen($string);
    }

    public static function limit(string $value, int $limit = 100, string $end = Str::ELLIPSIS): string
    {
        if (mb_strwidth($value, Str::UTF_8) <= $limit) {
            return $value;
        }

        return static::rtrim(mb_strimwidth($value, 0, $limit, '', Str::UTF_8)) . $end;
    }

    public static function lower(string $string, ?string $encoding = Str::UTF_8): string
    {
        return mb_strtolower($string, $encoding);
    }

    public static function ltrim(string $value, ?string $charlist = null): string
    {
        if ($charlist === null) {
            return preg_replace('~^[\s\x{FEFF}\x{200B}\x{200E}]+~u', '', $value) ?? ltrim($value);
        }

        return ltrim($value, $charlist);
    }

    public static function markdown(string $string): string
    {
        return Markdown::from($string)->toHtml();
    }

    public static function mask(string $string, string $character, int $index, ?int $length = null, ?string $encoding = Str::UTF_8): string
    {
        if ($character === '') {
            return $string;
        }

        $segment = static::substr($string, $index, $length, $encoding);

        if ($segment === '') {
            return $string;
        }

        $strlen = static::length($string, $encoding);
        $startIndex = $index;

        if ($index < 0) {
            $startIndex = $index < -$strlen ? 0 : $strlen + $index;
        }

        $start = static::substr($string, 0, $startIndex, $encoding);
        $segmentLen = static::length($segment, $encoding);
        $end = static::substr($string, $startIndex + $segmentLen);

        return $start . static::repeat(static::substr($character, 0, 1, $encoding), $segmentLen) . $end;
    }

    public static function match(string $pattern, string $subject): string
    {
        preg_match($pattern, $subject, $matches);

        if (!$matches) {
            return '';
        }

        return $matches[1] ?? $matches[0];
    }

    public static function matchAll(string $pattern, string $subject): array
    {
        preg_match_all($pattern, $subject, $matches);

        if (empty($matches[0])) {
            return [];
        }

        return $matches[1] ?? $matches[0];
    }

    public static function numbers(string $value): string
    {
        return preg_replace('/[^0-9]/', '', $value);
    }

    public static function padBoth(string $string, int $length, string $pad = ' ', ?string $encoding = Str::UTF_8): string
    {
        return mb_str_pad($string, $length, $pad, STR_PAD_BOTH, $encoding);
    }

    public static function padLeft(string $string, int $length, string $pad = ' ', ?string $encoding = Str::UTF_8): string
    {
        return mb_str_pad($string, $length, $pad, STR_PAD_LEFT, $encoding);
    }

    public static function padRight(string $string, int $length, string $pad = ' ', ?string $encoding = Str::UTF_8): string
    {
        return mb_str_pad($string, $length, $pad, STR_PAD_RIGHT, $encoding);
    }

    public static function password(int $length = 32, bool $specialChars = true): string
    {
        return wp_generate_password($length, $specialChars, false);
    }

    public static function plural(string $value, int $count = 2): string
    {
        return Pluralizer::plural($value, $count);
    }

    public static function position(string $haystack, string $needle, int $offset = 0, ?string $encoding = Str::UTF_8): int|bool
    {
        return mb_strpos($haystack, $needle, $offset, $encoding);
    }

    public static function random(int $length = 16): string
    {
        return static::password($length, false);
    }

    public static function remove(string|array $search, string|array $subject, bool $caseSensitive = true): string
    {
        return static::replace($search, '', $subject, $caseSensitive);
    }

    public static function repeat(string $string, int $times): string
    {
        return str_repeat($string, $times);
    }

    public static function replace(string|array $search, string|array $replace, string|array $subject, bool $caseSensitive = true): string|array
    {
        return $caseSensitive
            ? str_replace($search, $replace, $subject)
            : str_ireplace($search, $replace, $subject);
    }

    public static function replaceArray(string $search, array $replace, string $subject): string
    {
        $segments = static::explode($search, $subject);

        // array_shift is used instead of Arr helper class beause the array is
        // needed to be modified by reference.
        $result = array_shift($segments);

        foreach ($segments as $segment) {
            $result .= self::toStringOr(array_shift($replace) ?? $search, $search) . $segment;
        }

        return $result;
    }

    public static function replaceFirst(string $search, string $replace, string $subject): string
    {
        if ($search === '') {
            return $subject;
        }

        $position = static::position($subject, $search);

        if ($position !== false) {
            return static::substrReplace($subject, $replace, $position, static::length($search));
        }

        return $subject;
    }

    public static function replaceStart(string $search, string $replace, string $subject): string
    {
        if ($search !== '' && static::startsWith($subject, $search)) {
            return static::replaceFirst($search, $replace, $subject);
        }

        return $subject;
    }

    public static function replaceLast(string $search, string $replace, string $subject): string
    {
        if ($search === '') {
            return $subject;
        }

        $position = mb_strrpos($subject, $search);

        if ($position !== false) {
            return static::substrReplace($subject, $replace, $position, static::length($search));
        }

        return $subject;
    }

    public static function replaceEnd(string $search, string $replace, string $subject): string
    {
        if ($search !== '' && static::endsWith($subject, $search)) {
            return static::replaceLast($search, $replace, $subject);
        }

        return $subject;
    }

    public static function replaceMatches(array|string $pattern, callable|array|string $replace, array|string $subject, int $limit = -1): string|array|null
    {
        if (is_callable($replace)) {
            return preg_replace_callback($pattern, $replace, $subject, $limit);
        }

        return preg_replace($pattern, $replace, $subject, $limit);
    }

    public static function reverse(string $value): string
    {
        return implode(Arr::reverse(static::split($value)));
    }

    public static function rtrim(string $value, ?string $charlist = null): string
    {
        if ($charlist === null) {
            return preg_replace('~[\s\x{FEFF}\x{200B}\x{200E}]+$~u', '', $value) ?? rtrim($value);
        }

        return rtrim($value, $charlist);
    }

    public static function singular(string $value): string
    {
        return Pluralizer::singular($value);
    }

    public static function slug(string $string): string
    {
        return sanitize_title($string);
    }

    public static function snake(string $string, string $delimiter = '_'): string
    {
        if (!ctype_lower($string)) {
            $value = preg_replace('/\s+/u', '', static::ucwords($string));

            $value = static::lower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value));
        }

        return $value;
    }

    public static function split(string $string, string|int $pattern = 1, int $limit = -1, int $flags = 0, ?string $encoding = Str::UTF_8): array
    {
        if (filter_var($pattern, FILTER_VALIDATE_INT) !== false) {
            return mb_str_split($string, $pattern, $encoding);
        }

        return preg_split($pattern, $string, $limit, $flags);
    }

    public static function squish(string $value): string
    {
        return preg_replace('~(\s|\x{3164}|\x{1160})+~u', ' ', static::trim($value));
    }

    public static function start(string $value, string $prefix): string
    {
        $quoted = preg_quote($prefix, '/');

        return $prefix . preg_replace('/^(?:' . $quoted . ')+/u', '', $value);
    }

    public static function startsWith(string $haystack, array|string $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle !== '' && str_starts_with($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    public static function stripTags(string $string, array|string|null $allowedTags = null): string
    {
        return strip_tags($string, $allowedTags);
    }

    public static function studly(string $string): string
    {
        return static::of($string)
            ->replace(['-', '_'], ' ')
            ->explode()
            ->map(fn ($word) => static::ucfirst($word))
            ->join();
    }

    public static function substr(string $string, int $start = 0, ?int $length = null, ?string $encoding = Str::UTF_8): string
    {
        return mb_substr($string, $start, $length, $encoding);
    }

    public static function substrCount(string $haystack, string $needle, int $start = 0, ?int $length = null, ?string $encoding = Str::UTF_8): int
    {
        return mb_substr_count(static::substr($haystack, $start, $length, $encoding), $needle, $encoding);
    }

    public static function substrReplace(array|string $string, array|string $replace, int|array $offset = 0, int|array|null $length = null): array|string
    {
        return substr_replace($string, $replace, $offset, $length);
    }

    public static function swap(array $map, string $subject): string
    {
        return strtr($subject, $map);
    }

    public static function take(string $string, int $limit): string
    {
        if ($limit < 0) {
            return static::substr($string, $limit);
        }

        return static::substr($string, 0, $limit);
    }

    public static function title(string $value, ?string $encoding = Str::UTF_8): string
    {
        return static::convertCase($value, MB_CASE_TITLE, $encoding);
    }

    public static function toBase64(string $string): string
    {
        return base64_encode($string);
    }

    public static function toBoolean(string $string): bool
    {
        return filter_var($string, FILTER_VALIDATE_BOOLEAN);
    }

    public static function toDate(string $string, ?string $format = null, ?string $tz = null): Carbon
    {
        if (is_null($format)) {
            return Carbon::parse($string, $tz);
        }

        return Carbon::createFromFormat($format, $string, $tz);
    }

    public static function toFloat(string $string): float
    {
        return floatval($string);
    }

    public static function toInteger(string $string, int $base = 10): int
    {
        return intval($string, $base);
    }

    private static function toStringOr(mixed $value, string $fallback): string
    {
        try {
            return (string) $value;
        } catch (Throwable $e) {
            return $fallback;
        }
    }

    public static function trim(string $value, ?string $charlist = null): string
    {
        if ($charlist === null) {
            return preg_replace('~^[\s\x{FEFF}\x{200B}\x{200E}]+|[\s\x{FEFF}\x{200B}\x{200E}]+$~u', '', $value) ?? trim($value);
        }

        return trim($value, $charlist);
    }

    public static function ucfirst(string $string, ?string $encoding = Str::UTF_8): string
    {
        return mb_ucfirst($string, $encoding);
    }

    public static function ucsplit(string $string): array
    {
        return preg_split('/(?=\p{Lu})/u', $string, -1, PREG_SPLIT_NO_EMPTY);
    }

    public static function ucwords(string $string, string $separators = " \t\r\n\f\v"): string
    {
        return ucwords($string, $separators);
    }

    public static function unwrap(string $value, string $before, ?string $after = null): string
    {
        if (static::startsWith($value, $before)) {
            $value = static::substr($value, static::length($before));
        }

        if (static::endsWith($value, $after ??= $before)) {
            $value = static::substr($value, 0, -static::length($after));
        }

        return $value;
    }

    public static function upper(string $string): string
    {
        return mb_strtoupper($string);
    }

    public static function uuid(): string
    {
        return wp_generate_uuid4();
    }

    public static function wordCount(string $string, ?string $characters = null)
    {
        return str_word_count($string, 0, $characters);
    }

    public static function words(string $value, int $words = 100, string $end = Str::ELLIPSIS): string
    {
        preg_match('/^\s*+(?:\S++\s*+){1,' . $words . '}/u', $value, $matches);

        if (!isset($matches[0]) || static::length($value) === static::length($matches[0])) {
            return $value;
        }

        return rtrim($matches[0]) . $end;
    }

    public static function wordWrap(string $string, int $characters = 75, string $break = PHP_EOL, bool $cutLongWords = false): string
    {
        return wordwrap($string, $characters, $break, $cutLongWords);
    }

    public static function wrap(string $value, string $before, ?string $after = null): string
    {
        return $before . $value . ($after ??= $before);
    }
}
