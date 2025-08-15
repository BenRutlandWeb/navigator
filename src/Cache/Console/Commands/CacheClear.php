<?php

namespace Navigator\Cache\Console\Commands;

use Navigator\Cache\Repository;
use Navigator\Console\Command;

class CacheClear extends Command
{
    protected string $signature = 'cache:clear';

    protected string $description = 'Flush the application cache.';

    protected function handle(): void
    {
        if ($this->app->get(Repository::class)->flush()) {
            $this->success("Application cache cleared successfully")->terminate();
        }

        $this->error('Failed to clear cache');
    }
}
