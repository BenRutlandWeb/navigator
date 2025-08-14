<?php

namespace Navigator\Foundation\Console\Commands;

use Navigator\Console\Command;
use Navigator\Filesystem\Filesystem;

class StorageClear extends Command
{
    protected string $signature = 'storage:clear';

    protected string $description = 'Delete the Application storage files.';

    protected function handle(): void
    {
        $deleted = $this->app->get(Filesystem::class)->deleteDirectory(
            $this->app->path('storage/app')
        );

        if ($deleted) {
            $this->success('storage/app deleted')->terminate();
        }

        $this->error('storage/app couldn\'t be deleted');
    }
}
