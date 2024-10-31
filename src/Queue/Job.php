<?php

namespace Navigator\Queue;

use WP_Queue\Job as BaseJob;

abstract class Job extends BaseJob
{
    public static function dispatch(mixed ...$args): PendingDispatch
    {
        return new PendingDispatch(new static(...$args));
    }
}
