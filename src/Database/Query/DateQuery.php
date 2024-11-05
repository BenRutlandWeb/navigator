<?php

namespace Navigator\Database\Query;

use Carbon\CarbonImmutable;
use Closure;
use DateTimeInterface;
use Navigator\Collections\Arr;
use Navigator\Database\Query\Concerns\Relation;
use Navigator\Foundation\Concerns\Arrayable;

class DateQuery implements Arrayable
{
    public function __construct(protected array $query = [])
    {
        //
    }

    public static function make(array $query = []): static
    {
        return new static($query);
    }

    /** @param array<string, string|int>|null $date */
    public function where(string|callable $column, ?string $compare = null, ?array $date = null, Relation $relation = Relation::AND): static
    {
        $this->query['relation'] = $relation->value;

        if (is_callable($column)) {
            $this->query[] = $subQuery = new static;

            $column($subQuery);

            return $this;
        }

        $attributes = Arr::filter(compact('column', 'compare') + $date, function ($entry) {
            return !is_null($entry);
        });

        $this->query[] = $attributes;

        return $this;
    }

    /** @param array<string, string|int>|null $date */
    public function orWhere(string|callable $column, ?string $compare = null, ?array $date = null): static
    {
        return $this->where($column, $compare, $date, Relation::OR);
    }

    public function when($condition = null, ?callable $callback = null, ?callable $default = null): static
    {
        $condition = $condition instanceof Closure ? $condition($this) : $condition;

        if ($condition) {
            return $callback($this, $condition) ?? $this;
        } elseif ($default) {
            return $default($this, $condition) ?? $this;
        }

        return $this;
    }

    public function whereDateTime(string $column, string $compare, DateTimeInterface|string $datetime, Relation $relation = Relation::AND): static
    {
        $datetime = static::resolveDateTime($datetime);

        return $this->where($column, $compare, [
            'year'   => $datetime->format('Y'),
            'month'  => $datetime->format('m'),
            'day'    => $datetime->format('d'),
            'hour'   => $datetime->format('H'),
            'minute' => $datetime->format('i'),
            'second' => $datetime->format('s'),
        ], $relation);
    }

    public function orWhereDateTime(string $column, string $compare, DateTimeInterface|string $datetime): static
    {
        return $this->whereDateTime($column, $compare, $datetime, Relation::OR);
    }

    public function whereDate(string $column, string $compare, DateTimeInterface|string $date, Relation $relation = Relation::AND): static
    {
        $date = static::resolveDateTime($date);

        return $this->where($column, $compare, [
            'year'  => $date->format('Y'),
            'month' => $date->format('m'),
            'day'   => $date->format('d'),
        ], $relation);
    }

    public function orWhereDate(string $column, string $compare, DateTimeInterface|string $date): static
    {
        return $this->whereDate($column, $compare, $date, Relation::OR);
    }

    /** @param DateTimeInterface|int|string|array<int, DateTimeInterface|int|string> $year */
    public function whereYear(string $column, string $compare, DateTimeInterface|array|int|string $year, Relation $relation = Relation::AND): static
    {
        return $this->where($column, $compare, ['year' => static::formatDateTimeSegment($year, 'Y')], $relation);
    }

    /** @param DateTimeInterface|int|string|array<int, DateTimeInterface|int|string> $year */
    public function orWhereYear(string $column, string $compare, DateTimeInterface|array|int|string $year): static
    {
        return $this->whereYear($column, $compare, $year, Relation::OR);
    }

    /** @param DateTimeInterface|int|string|array<int, DateTimeInterface|int|string> $month */
    public function whereMonth(string $column, string $compare, DateTimeInterface|array|int|string $month, Relation $relation = Relation::AND): static
    {
        return $this->where($column, $compare, ['month' => static::formatDateTimeSegment($month, 'm')], $relation);
    }

    /** @param DateTimeInterface|int|string|array<int, DateTimeInterface|int|string> $month */
    public function orWhereMonth(string $column, string $compare, DateTimeInterface|array|int|string $month): static
    {
        return $this->whereMonth($column, $compare, $month, Relation::OR);
    }

    /** @param DateTimeInterface|int|string|array<int, DateTimeInterface|int|string> $week */
    public function whereWeek(string $column, string $compare, DateTimeInterface|array|int|string $week, Relation $relation = Relation::AND): static
    {
        return $this->where($column, $compare, ['week' => static::formatDateTimeSegment($week, 'W')], $relation);
    }

    /** @param DateTimeInterface|int|string|array<int, DateTimeInterface|int|string> $week */
    public function orWhereWeek(string $column, string $compare, DateTimeInterface|array|int|string $week): static
    {
        return $this->whereWeek($column, $compare, $week, Relation::OR);
    }

    /** @param DateTimeInterface|int|string|array<int, DateTimeInterface|int|string> $day */
    public function whereDay(string $column, string $compare, DateTimeInterface|array|int|string $day, Relation $relation = Relation::AND): static
    {
        return $this->where($column, $compare, ['day' => static::formatDateTimeSegment($day, 'd')], $relation);
    }

    /** @param DateTimeInterface|int|string|array<int, DateTimeInterface|int|string> $day */
    public function orWhereDay(string $column, string $compare, DateTimeInterface|array|int|string $day): static
    {
        return $this->whereDay($column, $compare, $day, Relation::OR);
    }

    /** @param DateTimeInterface|int|string|array<int, DateTimeInterface|int|string> $day */
    public function whereWeekDay(string $column, string $compare, DateTimeInterface|array|int|string $day, Relation $relation = Relation::AND): static
    {
        return $this->where($column, $compare, ['dayofweek_iso' => static::formatDateTimeSegment($day, 'w')], $relation);
    }

    /** @param DateTimeInterface|int|string|array<int, DateTimeInterface|int|string> $day */
    public function orWhereWeekDay(string $column, string $compare, DateTimeInterface|array|int|string $day): static
    {
        return $this->whereWeekDay($column, $compare, $day, Relation::OR);
    }

    public function whereTime(string $column, string $compare, DateTimeInterface|string $time, Relation $relation = Relation::AND): static
    {
        $time = static::resolveDateTime($time);

        return $this->where($column, $compare, [
            'hour'   => $time->format('H'),
            'minute' => $time->format('i'),
            'second' => $time->format('s'),
        ], $relation);
    }

    public function orWhereTime(string $column, string $compare, DateTimeInterface|string $time): static
    {
        return $this->whereTime($column, $compare, $time, Relation::OR);
    }

    /** @param DateTimeInterface|int|string|array<int, DateTimeInterface|int|string> $hour */
    public function whereHour(string $column, string $compare, DateTimeInterface|array|int|string $hour, Relation $relation = Relation::AND): static
    {
        return $this->where($column, $compare, ['hour' => static::formatDateTimeSegment($hour, 'H')], $relation);
    }

    /** @param DateTimeInterface|int|string|array<int, DateTimeInterface|int|string> $hour */
    public function orWhereHour(string $column, string $compare, DateTimeInterface|array|int|string $hour): static
    {
        return $this->whereHour($column, $compare, $hour, Relation::OR);
    }

    /** @param DateTimeInterface|int|string|array<int, DateTimeInterface|int|string> $minute */
    public function whereMinute(string $column, string $compare, DateTimeInterface|array|int|string $minute, Relation $relation = Relation::AND): static
    {
        return $this->where($column, $compare, ['minute' => static::formatDateTimeSegment($minute, 'i')], $relation);
    }

    /** @param DateTimeInterface|int|string|array<int, DateTimeInterface|int|string> $minute */
    public function orWhereMinute(string $column, string $compare, DateTimeInterface|array|int|string $minute): static
    {
        return $this->whereMinute($column, $compare, $minute, Relation::OR);
    }

    /** @param DateTimeInterface|int|string|array<int, DateTimeInterface|int|string> $second */
    public function whereSecond(string $column, string $compare, DateTimeInterface|array|int|string $second, Relation $relation = Relation::AND): static
    {
        return $this->where($column, $compare, ['second' => static::formatDateTimeSegment($second, 's')], $relation);
    }

    /** @param DateTimeInterface|int|string|array<int, DateTimeInterface|int|string> $second */
    public function orWhereSecond(string $column, string $compare, DateTimeInterface|array|int|string $second): static
    {
        return $this->whereSecond($column, $compare, $second, Relation::OR);
    }

    public static function resolveDateTime(DateTimeInterface|string $datetime): DateTimeInterface
    {
        return $datetime instanceof DateTimeInterface ? $datetime : CarbonImmutable::create($datetime);
    }

    /**
     * @param DateTimeInterface|int|string|array<int, DateTimeInterface|int|string> $segment
     * @return int|array<int, int>
     * */
    public static function formatDateTimeSegment(DateTimeInterface|int|string|array $segment, string $format): int|array
    {
        if (is_array($segment)) {
            return Arr::map($segment, fn($s) => static::formatDateTimeSegment($s, $format));
        }

        if (!is_numeric($segment)) {
            $segment = static::resolveDateTime($segment)->format($format);
        }

        return (int) $segment;
    }

    public function toArray(): array
    {
        $return = [];

        foreach ($this->query as $key => $value) {
            if ($value instanceof static) {
                $return[$key] = $value->toArray();
            } else {
                $return[$key] = $value;
            }
        }

        return $return;
    }

    public function empty(): bool
    {
        return empty($this->query);
    }

    public function notEmpty(): bool
    {
        return !$this->empty();
    }
}
