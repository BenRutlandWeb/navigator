<?php

namespace Navigator\Queue;

use Navigator\Database\Connection;
use Navigator\Events\Dispatcher;
use Navigator\Foundation\Application;
use Navigator\Foundation\ServiceProvider;
use Navigator\Queue\Console\Commands\MakeJob;
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
        $db = $this->app->get(Connection::class);

        if (!$db->hasTable('queue_jobs') || !$db->hasTable('queue_failures')) {
            wp_queue_install_tables();
        }

        $this->app->get(Dispatcher::class)->listen('init', function () {
            $this->app->get(Queue::class)->cron(3, 1);
        });

        $this->commands([
            MakeJob::class,
        ]);
    }
}
