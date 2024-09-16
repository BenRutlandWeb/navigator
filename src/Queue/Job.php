<?php

namespace Navigator\Queue;

use WP_Queue\Job as BaseJob;

use function Navigator\queue;

abstract class Job extends BaseJob
{
    public static function dispatch(mixed ...$args): bool
    {
        return queue()->push(new static(...$args));
    }
}
