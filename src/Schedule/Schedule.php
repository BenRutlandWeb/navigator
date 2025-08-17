<?php

namespace Navigator\Schedule;

use Navigator\Events\Dispatcher;
use Navigator\Queue\Job;

class Schedule
{
    protected array $events = [];

    public function __construct(protected Dispatcher $dispatcher)
    {
        //
    }

    public function call(callable $callback, array $parameters = []): Event
    {
        return $this->events[] = (new Event($callback, $parameters))->name(
            $this->resolveName($callback)
        );
    }

    public function job(Job $job): Event
    {
        return $this->call([$job, 'handle']);
    }

    public function registerScheduledEvents(): void
    {
        foreach ($this->events as $event) {
            if ($event->filtersPass()) {
                $this->registerScheduledEvent($event);
            }
        }
    }

    public function registerScheduledEvent(Event $event): void
    {
        $this->dispatcher->listen(
            $name = $event->getSummaryForDisplay(),
            $event->event()
        );

        if (!wp_next_scheduled($name)) {
            wp_schedule_single_event($event->nextRunDate()->format('U'), $name);
        }
    }

    public function resolveName(callable $callback): string
    {
        if (is_string($callback)) {
            return $callback;
        }

        if (is_array($callback)) {
            return basename(is_string($callback[0]) ? $callback[0] : get_class($callback[0]));
        }

        return basename(get_class($callback));
    }
}
