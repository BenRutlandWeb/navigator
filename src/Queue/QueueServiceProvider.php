<?php

namespace Navigator\Queue;

use Navigator\Foundation\Application;
use Navigator\Foundation\ServiceProvider;
use Navigator\Queue\Console\Commands\MakeJob;
use Navigator\Queue\Console\Commands\QueueTables;
use Navigator\Queue\Console\Commands\QueueWork;
use Navigator\Schedule\Schedule;
use WP_Queue\Queue;

class QueueServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Queue::class, function (Application $app) {
            $queue = wp_queue($app->env('QUEUE_CONNECTION', 'sync'));

            PendingDispatch::setQueue($queue);

            return $queue;
        });
    }

    public function boot(): void
    {
        $queue = $this->app->get(Queue::class);

        $this->app->get(Schedule::class)->job(new ProcessQueue($queue))->everyFiveMinutes();

        $this->commands([
            MakeJob::class,
            QueueTables::class,
            QueueWork::class,
        ]);
    }
}
