<?php

namespace Navigator\Queue;

use Carbon\CarbonInterface;
use WP_Queue\Queue;

class PendingDispatch
{
    protected static Queue $queue;

    protected int $delay = 0;

    public function __construct(protected Job $job)
    {
        //
    }

    public function delay(CarbonInterface|int $delay = 0): static
    {
        $this->delay = $delay instanceof CarbonInterface
            ? $delay->diffInSeconds()
            : $delay;

        return $this;
    }

    public static function setQueue(Queue $queue): void
    {
        static::$queue = $queue;
    }

    public function __destruct()
    {
        static::$queue->push($this->job, $this->delay);
    }
}
