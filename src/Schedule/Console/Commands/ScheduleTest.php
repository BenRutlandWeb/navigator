<?php

namespace Navigator\Schedule\Console\Commands;

use Navigator\Console\Command;

use function Navigator\collect;

class ScheduleTest extends Command
{
    protected string $signature = 'schedule:test';

    protected string $description = 'Run a scheduled command.';

    protected function handle(): void
    {
        $test = $this->ask(
            'Which command would you like to run?',
            collect(_get_cron_array())
                ->map(fn($hooks) => array_keys($hooks))
                ->flatten()
                ->toArray()
        );

        $this->info('Running...')->newLine();

        if ($this->call("cron event run {$test}")) {
            $this->replacePreviousLine('')->success("Done");
        }
    }
}
