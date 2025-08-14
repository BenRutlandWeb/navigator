<?php

namespace Navigator\Queue\Console\Commands;

use Navigator\Console\Command;

class QueueTables extends Command
{
    protected string $signature = 'queue:tables';

    protected string $description = 'Create the queue jobs database tables.';

    protected function handle(): void
    {
        $this->header('Navigator', 'Create the queue database tables');

        wp_queue_install_tables();

        $this->success('Database tables created');
    }
}
