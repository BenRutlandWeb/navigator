<?php

namespace Navigator\Schedule\Console\Commands;

use Navigator\Console\Command;

class ScheduleRun extends Command
{
    protected string $signature = 'schedule:run';

    protected string $description = 'Run the scheduled commands.';

    protected function handle(): void
    {
        $this->warning('The schedule:run process can take some time')->newLine();

        if ($this->call('cron event run --all', launch: false)) {
            $this->replacePreviousLine('')->success('Executed the scheduled tasks');
        }
    }
}
