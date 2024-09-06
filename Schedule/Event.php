<?php

namespace Navigator\Schedule;

use Closure;
use Cron\CronExpression;
use DateTimeInterface;
use DateTimeZone;
use Navigator\Schedule\Concerns\ManagesFrequencies;

class Event
{
    use ManagesFrequencies;

    protected $callback;

    public string $expression = '* * * * *';

    public string $description = '';

    protected array $filters = [];

    protected array $rejects = [];

    public function __construct(callable $callback, protected array $parameters = [], protected DateTimeZone|string|null $timezone = null)
    {
        $this->callback = $callback;
    }

    public function getExpression(): string
    {
        return $this->expression;
    }

    public function event(): Closure
    {
        return function () {
            return call_user_func($this->callback, $this->parameters);
        };
    }

    public function name(string $name): static
    {
        $this->description = $name;

        return $this;
    }

    public function getSummaryForDisplay(): string
    {
        return $this->description;
    }

    public function nextRunDate(string $currentTime = 'now', int $nth = 0, bool $allowCurrentDate = false): DateTimeInterface
    {
        return (new CronExpression($this->getExpression()))
            ->getNextRunDate($currentTime, $nth, $allowCurrentDate, $this->timezone);
    }

    public function filtersPass(): bool
    {
        foreach ($this->filters as $callback) {
            if (!call_user_func($callback)) {
                return false;
            }
        }

        foreach ($this->rejects as $callback) {
            if (call_user_func($callback)) {
                return false;
            }
        }

        return true;
    }

    public function when(callable|bool $callback): static
    {
        $this->filters[] = is_callable($callback) ? $callback : function () use ($callback) {
            return $callback;
        };

        return $this;
    }

    public function skip(callable|bool $callback): static
    {
        $this->rejects[] = is_callable($callback) ? $callback : function () use ($callback) {
            return $callback;
        };

        return $this;
    }
}
