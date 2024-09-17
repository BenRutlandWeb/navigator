<?php

namespace Navigator\Queue;

use WP_Queue\Cron;
use WP_Queue\Queue;

class ProcessQueue extends Job
{
    public function __construct(public Queue $queue, public int $attempts = 3)
    {
        //
    }

    public function handle(): void
    {
        $worker = $this->queue->worker($this->attempts);

        $cron = new Cron(get_class(), $worker, 1);

        $cron->cron_worker();
    }
}
