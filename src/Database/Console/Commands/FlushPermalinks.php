<?php

namespace Navigator\Database\Console\Commands;

use Navigator\Console\Command;

class FlushPermalinks extends Command
{
    protected string $signature = 'flush-rules';

    protected string $description = 'Remove and then recreate rewrite rules.';

    protected function handle(): void
    {
        flush_rewrite_rules();

        $this->success('Rewrite rules flushed.');
    }
}
