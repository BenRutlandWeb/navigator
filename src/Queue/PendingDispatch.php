<?php

namespace Navigator\Queue;

use WP_Queue\Queue;

class PendingDispatch
{
    protected static Queue $queue;

    public function __construct(protected Job $job)
    {
        //
    }

    public static function setQueue(Queue $queue): void
    {
        static::$queue = $queue;
    }

    public function __destruct()
    {
        static::$queue->push($this->job);
    }
}
