<?php

namespace Navigator\Queue;

use WP_Queue\Job as BaseJob;

abstract class Job extends BaseJob
{
    public static function dispatch(mixed ...$args): PendingDispatch
    {
        $job = new static(...$args);

        $dispatch = new PendingDispatch($job);

        if (isset($notification->delay)) {
            $dispatch->delay($notification->delay);
        }

        return $dispatch;
    }
}
