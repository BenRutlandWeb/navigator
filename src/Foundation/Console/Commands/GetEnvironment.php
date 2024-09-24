<?php

namespace Navigator\Foundation\Console\Commands;

use Navigator\Console\Command;

class GetEnvironment extends Command
{
    protected string $signature = 'env:get';

    protected string $description = 'Get the environment.';

    protected function handle(): void
    {
        $this->line("The current environment is: {$this->app->environment->value}");
    }
}
