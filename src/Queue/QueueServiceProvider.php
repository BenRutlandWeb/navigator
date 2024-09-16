<?php

namespace Navigator\Queue;

use Navigator\Events\Dispatcher;
use Navigator\Foundation\Application;
use Navigator\Foundation\ServiceProvider;
use Navigator\Queue\Console\Commands\MakeJob;
use Navigator\Queue\Console\Commands\QueueTable;
use WP_Queue\Queue;

class QueueServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Queue::class, function (Application $app) {
            return wp_queue($app->env('QUEUE_CONNECTION', 'sync'));
        });
    }

    public function boot(): void
    {
        $this->app->get(Dispatcher::class)->listen('init', function () {
            $this->app->get(Queue::class)->cron(3, 1);
        });

        $this->commands([
            MakeJob::class,
            QueueTable::class,
        ]);
    }
}
