<?php

namespace Navigator\Foundation\Console\Commands;

use Navigator\Console\Command;
use Navigator\Filesystem\Filesystem;
use Navigator\Foundation\ServicesRepository;

class ClearCompiled extends Command
{
    protected string $signature = 'clear-compiled';

    protected string $description = 'Remove the compiled class file.';

    protected function handle(): void
    {
        $deleted = $this->app->get(Filesystem::class)->delete(
            $this->app->make(ServicesRepository::class)->getPath()
        );

        if ($deleted) {
            $this->success('Compiled services file removed successfully')->terminate();
        }

        $this->error('Compiled services file couldn\'t be removed');
    }
}
