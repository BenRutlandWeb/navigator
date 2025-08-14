<?php

namespace Navigator\Schedule\Console\Commands;

use Navigator\Console\Command;

class ScheduleList extends Command
{
    protected string $signature = 'schedule:list';

    protected string $description = 'List all scheduled tasks.';

    protected function handle(): void
    {
        $this->header('Navigator', 'List all scheduled tasks');

        $this->call('cron event list', launch: false);
    }
}
