<?php

namespace Navigator\Routing\Console\Commands;

use Navigator\Console\Command;

class RouteClear extends Command
{
    protected string $signature = 'route:clear';

    protected string $description = 'Flush the permalink cache.';

    protected function handle(): void
    {
        $this->header('Navigator', 'Flush the permalink cache');

        if ($this->callSilently('rewrite flush')) {
            $this->success('Permalink cache flushed.');
        }
    }
}
