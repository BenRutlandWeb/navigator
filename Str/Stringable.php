<?php

namespace Navigator\Str;

use Carbon\Carbon;
use JsonSerializable;
use Navigator\Collections\Collection;
use Stringable as BaseStringable;

class Stringable implements BaseStringable, JsonSerializable
{
    public function __construct(protected string $value = '')
    {
        //
    }

    public function after(string $search): static
    {
        return new static(Str::after($this->value, $search));
    }

    public function afterLast(string $search): static
    {
        return new static(Str::afterLast($this->value, $search));
    }

    public function append(string ...$values): static
    {
        return new static($this->value . implode('', $values));
    }

    public function basename(string $suffix = ''): static
    {
        return new static(Str::basename($this->value, $suffix));
    }

    public function before(string $search): static
    {
        return new static(Str::before($this->value, $search));
    }

    public function beforeLast(string $search): static
    {
        return new static(Str::beforeLast($this->value, $search));
    }

    public function between(string $from, string $to): static
    {
        return new static(Str::between($this->value, $from, $to));
    }

    public function betweenFirst(string $from, string $to): static
    {
        return new static(Str::betweenFirst($this->value, $from, $to));
    }

    public function camel(): static
    {
        return new static(Str::camel($this->value));
    }

    public function charAt(int $index): static
    {
        return new static(Str::charAt($this->value, $index));
    }

    public function classBasename(): static
    {
        return new static(Str::classBasename($this->value));
    }

    public function dirname(int $levels = 1): static
    {
        return new static(Str::dirname($this->value, $levels));
    }

    public function contains(iterable|string $needles, bool $ignoreCase = false): bool
    {
        return Str::contains($this->value, $needles, $ignoreCase);
    }

    public function containsAll(iterable|string $needles, bool $ignoreCase = false): bool
    {
        return Str::containsAll($this->value, $needles, $ignoreCase);
    }

    public function convertCase(int $mode = MB_CASE_FOLD, ?string $encoding = Str::UTF_8): static
    {
        return new static(Str::convertCase($this->value, $mode, $encoding));
    }

    public function endsWith(iterable|string $needles): bool
    {
        return Str::endsWith($this->value, $needles);
    }

    public function exactly(string $value): bool
    {
        return Str::exactly($this->value, $value);
    }

    /** @return Collection<int, string> */
    public function explode(string $separator = ' ', int $limit = PHP_INT_MAX): Collection
    {
        return  Collection::make(Str::explode($this->value, $separator, $limit));
    }

    public function finish(string $cap): static
    {
        return new static(Str::finish($this->value, $cap));
    }

    public function fromBase64(bool $strict = false): static
    {
        return new static(Str::fromBase64($this->value, $strict));
    }

    public function headline(): static
    {
        return new static(Str::headline($this->value));
    }

    public function is(string|array $pattern): bool
    {
        return Str::is($pattern, $this->value);
    }

    public function isEmpty(): bool
    {
        return Str::isEmpty($this->value);
    }

    public function isNotEmpty(): bool
    {
        return Str::isNotEmpty($this->value);
    }

    public function isJson(): bool
    {
        return Str::isJson($this->value);
    }

    public function isMatch(string|array $pattern): bool
    {
        return Str::isMatch($pattern, $this->value);
    }

    public function isUrl(): bool
    {
        return Str::isUrl($this->value);
    }

    public function isUuid(): bool
    {
        return Str::isUuid($this->value);
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }

    public function kebab(): static
    {
        return new static(Str::kebab($this->value));
    }

    public function lcfirst(): static
    {
        return new static(Str::lcfirst($this->value));
    }

    public function length(): int
    {
        return Str::length($this->value);
    }

    public function limit(int $limit = 100, string $end = Str::ELLIPSIS): static
    {
        return new static(Str::limit($this->value, $limit, $end));
    }

    public function lower(?string $encoding = Str::UTF_8): static
    {
        return new static(Str::lower($this->value, $encoding));
    }

    public function ltrim(?string $charlist = null): static
    {
        return new static(Str::ltrim($this->value, $charlist));
    }

    public function markdown(): static
    {
        return new static(Str::markdown($this->value));
    }

    public function mask(string $character, int $index, ?int $length = null, ?string $encoding = Str::UTF_8): static
    {
        return new static(Str::mask($this->value, $character, $index, $length, $encoding));
    }

    public function match(string $pattern): static
    {
        return new static(Str::match($pattern, $this->value));
    }

    public function matchAll(string $pattern): Collection
    {
        return  Collection::make(Str::matchAll($pattern, $this->value));
    }

    public function newLine(int $count = 1): static
    {
        return $this->append(Str::repeat(PHP_EOL, $count));
    }

    public function numbers(): static
    {
        return new static(Str::numbers($this->value));
    }

    public function padBoth(int $length, string $pad = ' ', ?string $encoding = Str::UTF_8): static
    {
        return new static(Str::padBoth($this->value, $length, $pad, $encoding));
    }

    public function padLeft(int $length, string $pad = ' ', ?string $encoding = Str::UTF_8): static
    {
        return new static(Str::padLeft($this->value, $length, $pad, $encoding));
    }

    public function padRight(int $length, string $pad = ' ', ?string $encoding = Str::UTF_8): static
    {
        return new static(Str::padRight($this->value, $length, $pad, $encoding));
    }

    /** @param (callable(): string) $callback */
    public function pipe(callable $callback): static
    {
        return new static($callback($this));
    }

    public function plural(int $count = 2): static
    {
        return new static(Str::plural($this->value, $count));
    }

    public function position(string $needle, int $offset = 0, ?string $encoding = Str::UTF_8): static
    {
        return new static(Str::position($this->value, $needle, $offset, $encoding));
    }

    public function prepend(string ...$values): static
    {
        return new static(implode('', $values) . $this->value);
    }

    public function remove(string|array $search, bool $caseSensitive = true): static
    {
        return new static(Str::remove($search, $this->value, $caseSensitive));
    }

    public function repeat(int $times): static
    {
        return new static(Str::repeat($this->value, $times));
    }

    public function replace(string|array $search, string|array $replace, bool $caseSensitive = true): static
    {
        return new static(Str::replace($search, $replace, $this->value, $caseSensitive));
    }

    public function replaceArray(string $search, array $replace): static
    {
        return new static(Str::replaceArray($search, $replace, $this->value));
    }

    public function replaceFirst(string $search, string $replace): static
    {
        return new static(Str::replaceFirst($search, $replace, $this->value));
    }

    public function replaceStart(string $search, string $replace): static
    {
        return new static(Str::replaceStart($search, $replace, $this->value));
    }

    public function replaceLast(string $search, string $replace): static
    {
        return new static(Str::replaceLast($search, $replace, $this->value));
    }

    public function replaceEnd(string $search, string $replace): static
    {
        return new static(Str::replaceEnd($search, $replace, $this->value));
    }

    public function replaceMatches(array|string $pattern, callable|array|string $replace, int $limit = -1): static
    {
        return new static(Str::replaceMatches($pattern, $replace, $this->value, $limit));
    }

    public function reverse(): static
    {
        return new static(Str::reverse($this->value));
    }

    public function rtrim(?string $charlist = null): static
    {
        return new static(Str::rtrim($this->value, $charlist));
    }

    public function singular(): static
    {
        return new static(Str::singular($this->value));
    }

    public function slug(): static
    {
        return new static(Str::slug($this->value));
    }

    public function snake(): static
    {
        return new static(Str::snake($this->value));
    }

    public function split(string|int $pattern = 1, int $limit = -1, int $flags = 0, ?string $encoding = Str::UTF_8): Collection
    {
        return Collection::make(Str::split($this->value, $pattern, $limit, $flags, $encoding));
    }

    public function squish(): static
    {
        return new static(Str::squish($this->value));
    }

    public function start(string $cap): static
    {
        return new static(Str::start($this->value, $cap));
    }

    public function startsWith(iterable|string $needles): bool
    {
        return Str::startsWith($this->value, $needles);
    }

    public function stripTags(array|string|null $allowedTags = null): static
    {
        return new static(Str::stripTags($this->value, $allowedTags));
    }

    public function studly(): static
    {
        return new static(Str::studly($this->value));
    }

    public function substr(int $offset, ?int $length = null): static
    {
        return new static(Str::substr($this->value, $offset, $length));
    }

    public function substrCount(string $needle, int $start = 0, ?int $length = null, ?string $encoding = Str::UTF_8): int
    {
        return Str::substrCount($this->value,  $needle, $start, $length, $encoding);
    }

    public function substrReplace(array|string $replace, int|array $offset = 0, null|int|array $length = null): static
    {
        return new static(Str::substrReplace($this->value, $replace, $offset, $length));
    }

    public function swap(array $map): static
    {
        return new static(Str::swap($map, $this->value));
    }

    public function take(int $limit): static
    {
        return new static(Str::take($this->value, $limit));
    }

    /** @param (callable(static)) $callback */
    public function tap(callable $callback): static
    {
        $callback($this);

        return $this;
    }

    public function title(): static
    {
        return new static(Str::title($this->value));
    }

    public function toBase64(): static
    {
        return new static(Str::toBase64($this->value));
    }

    public function toBoolean(): bool
    {
        return Str::toBoolean($this->value);
    }

    public function toDate(?string $format = null, ?string $tz = null): Carbon
    {
        return Str::toDate($this->value, $format, $tz);
    }

    public function toFloat(): float
    {
        return Str::toFloat($this->value);
    }

    public function toInteger(int $base = 10): int
    {
        return Str::toInteger($this->value, $base);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function trim(?string $charlist = null): static
    {
        return new static(Str::trim($this->value, $charlist));
    }

    public function ucfirst(): static
    {
        return new static(Str::ucfirst($this->value));
    }

    public function ucsplit(): Collection
    {
        return Collection::make(Str::ucsplit($this->value));
    }

    public function ucwords(string $separators = " \t\r\n\f\v"): static
    {
        return new static(Str::ucwords($this->value, $separators));
    }

    public function unwrap(string $before, ?string $after = null): static
    {
        return new static(Str::unwrap($this->value, $before, $after));
    }

    public function upper(?string $encoding = Str::UTF_8): static
    {
        return new static(Str::upper($this->value, $encoding));
    }

    public function words(int $words = 100, string $end = Str::ELLIPSIS): static
    {
        return new static(Str::words($this->value, $words, $end));
    }

    public function wordCount(?string $characters = null): static
    {
        return new static(Str::wordCount($this->value, $characters));
    }

    public function wordWrap(int $characters = 75, string $break = PHP_EOL, bool $cutLongWords = false): static
    {
        return new static(Str::wordWrap($this->value,  $characters, $break, $cutLongWords));
    }

    public function wrap(string $before, ?string $after = null): static
    {
        return new static(Str::wrap($this->value, $before, $after));
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
