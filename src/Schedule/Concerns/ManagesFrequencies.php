<?php

namespace Navigator\Schedule\Concerns;

use Closure;
use DateInterval;
use DateTime;
use DateTimeZone;

trait ManagesFrequencies
{
    public function cron(string $expression): static
    {
        $this->expression = $expression;

        return $this;
    }

    public function between(string $startTime, string $endTime): static
    {
        return $this->when($this->inTimeInterval($startTime, $endTime));
    }

    public function unlessBetween(string $startTime, string $endTime): static
    {
        return $this->skip($this->inTimeInterval($startTime, $endTime));
    }

    private function inTimeInterval(string $startTime, string $endTime): Closure
    {
        $timezone = $this->timezone instanceof DateTimeZone ?: new DateTimeZone($this->timezone);

        [$now, $startTime, $endTime] = [
            new DateTime('now', $timezone),
            DateTime::createFromFormat('H:i', $startTime, $timezone),
            DateTime::createFromFormat('H:i', $endTime, $timezone),
        ];

        $oneDay = new DateInterval('P1D');

        if ($endTime < $startTime) {
            if ($startTime > $now) {
                $startTime->sub($oneDay);
            } else {
                $endTime->add($oneDay);
            }
        }

        return function () use ($now, $startTime, $endTime) {
            return $now > $startTime && $now < $endTime;
        };
    }

    public function everyMinute(): static
    {
        return $this->spliceIntoPosition(1, '*');
    }

    public function everyTwoMinutes(): static
    {
        return $this->spliceIntoPosition(1, '*/2');
    }

    public function everyThreeMinutes(): static
    {
        return $this->spliceIntoPosition(1, '*/3');
    }

    public function everyFourMinutes(): static
    {
        return $this->spliceIntoPosition(1, '*/4');
    }

    public function everyFiveMinutes(): static
    {
        return $this->spliceIntoPosition(1, '*/5');
    }

    public function everyTenMinutes(): static
    {
        return $this->spliceIntoPosition(1, '*/10');
    }

    public function everyFifteenMinutes(): static
    {
        return $this->spliceIntoPosition(1, '*/15');
    }

    public function everyThirtyMinutes(): static
    {
        return $this->spliceIntoPosition(1, '0,30');
    }

    public function hourly(): static
    {
        return $this->spliceIntoPosition(1, 0);
    }

    public function hourlyAt(array|int $offset): static
    {
        $offset = is_array($offset) ? implode(',', $offset) : $offset;

        return $this->spliceIntoPosition(1, $offset);
    }

    public function everyTwoHours(): static
    {
        return $this->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, '*/2');
    }


    public function everyThreeHours(): static
    {
        return $this->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, '*/3');
    }

    public function everyFourHours(): static
    {
        return $this->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, '*/4');
    }

    public function everySixHours(): static
    {
        return $this->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, '*/6');
    }

    public function daily(): static
    {
        return $this->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, 0);
    }

    public function at(string $time): static
    {
        return $this->dailyAt($time);
    }

    public function dailyAt(string $time): static
    {
        $segments = explode(':', $time);

        return $this->spliceIntoPosition(2, (int) $segments[0])
            ->spliceIntoPosition(1, count($segments) === 2 ? (int) $segments[1] : '0');
    }

    public function twiceDaily(int $first = 1, int $second = 13): static
    {
        $hours = $first . ',' . $second;

        return $this->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, $hours);
    }

    public function weekdays(): static
    {
        return $this->days('1-5');
    }

    public function weekends(): static
    {
        return $this->days('6,0');
    }

    public function mondays(): static
    {
        return $this->days(1);
    }

    public function tuesdays(): static
    {
        return $this->days(2);
    }

    public function wednesdays(): static
    {
        return $this->days(3);
    }

    public function thursdays(): static
    {
        return $this->days(4);
    }

    public function fridays(): static
    {
        return $this->days(5);
    }

    public function saturdays(): static
    {
        return $this->days(6);
    }

    public function sundays(): static
    {
        return $this->days(0);
    }

    public function weekly(): static
    {
        return $this->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(5, 0);
    }

    public function weeklyOn(int $dayOfWeek, string $time = '0:0'): static
    {
        $this->dailyAt($time);

        return $this->days($dayOfWeek);
    }

    public function monthly(): static
    {
        return $this->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(3, 1);
    }

    public function monthlyOn(int $dayOfMonth = 1, string $time = '0:0'): static
    {
        $this->dailyAt($time);

        return $this->spliceIntoPosition(3, $dayOfMonth);
    }

    public function twiceMonthly(int $first = 1, int $second = 16, string $time = '0:0'): static
    {
        $daysOfMonth = $first . ',' . $second;

        $this->dailyAt($time);

        return $this->spliceIntoPosition(3, $daysOfMonth);
    }

    public function lastDayOfMonth(string $time = '0:0'): static
    {
        $this->dailyAt($time);

        return $this->spliceIntoPosition(3, (new DateTime())->format('t'));
    }

    public function quarterly(): static
    {
        return $this->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(3, 1)
            ->spliceIntoPosition(4, '1-12/3');
    }

    public function yearly(): static
    {
        return $this->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(3, 1)
            ->spliceIntoPosition(4, 1);
    }

    public function yearlyOn(int $month = 1, int|string $dayOfMonth = 1, string $time = '0:0'): static
    {
        $this->dailyAt($time);

        return $this->spliceIntoPosition(3, $dayOfMonth)
            ->spliceIntoPosition(4, $month);
    }

    public function days(string|int ...$days): static
    {
        return $this->spliceIntoPosition(5, implode(',', $days));
    }

    public function timezone(DateTimeZone|string $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }

    protected function spliceIntoPosition(int $position, string $value): static
    {
        $segments = explode(' ', $this->expression);

        $segments[$position - 1] = $value;

        return $this->cron(implode(' ', $segments));
    }
}
