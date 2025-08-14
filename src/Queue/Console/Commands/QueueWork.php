<?php

namespace Navigator\Queue\Console\Commands;

use Navigator\Console\Command;
use WP_Queue\Queue;

class QueueWork extends Command
{
    protected string $signature = 'queue:work
                                       {--once : Only process the next job on the queue.}
                                       {--max-jobs=0 : The number of jobs to process before stopping.}
                                       {--tries=1 : Number of times to attempt a job before logging it failed.}';

    protected string $description = 'Start processing jobs on the queue as a daemon.';

    protected function handle(): void
    {
        $this->info('Starting queue worker.');

        $queue = $this->app->get(Queue::class);

        if ($this->option('once')) {
            $this->process($queue);
            return;
        }

        if (($maxJobs = $this->option('max-jobs')) > 0) {
            for ($i = 0; $i < $maxJobs; $i++) {
                $this->process($queue);
            }

            return;
        }

        while (true) {
            if (!$this->process($queue)) {
                break;
            }
        }
    }

    public function process(Queue $queue): bool
    {
        if ($success = $queue->worker($this->option('tries'))->process()) {
            $this->success('Job processed');
        }

        return $success;
    }
}
