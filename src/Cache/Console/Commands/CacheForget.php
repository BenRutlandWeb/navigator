<?php

namespace Navigator\Cache\Console\Commands;

use Navigator\Cache\Repository;
use Navigator\Console\Command;

class CacheForget extends Command
{
    protected string $signature = 'cache:forget {key : The key to remove}';

    protected string $description = 'Remove an item from the cache.';

    protected function handle(): void
    {
        $this->app->get(Repository::class)->forget(
            $key = $this->argument('key')
        );

        $this->success("The [{$key}] key has been removed from the cache.");
    }
}
