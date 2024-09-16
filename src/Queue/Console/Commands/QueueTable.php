<?php

namespace Navigator\Queue\Console\Commands;

use Navigator\Console\Command;

class QueueTable extends Command
{
    protected string $signature = 'queue:table';

    protected string $description = 'Create the queue jobs database tables.';

    protected function handle(): void
    {
        wp_queue_install_tables();

        $this->success('Database tables created.');
    }
}
