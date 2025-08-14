<?php

namespace Navigator\Foundation\Console\Commands;

use Navigator\Collections\Arr;
use Navigator\Console\Command;
use Navigator\Foundation\Environment;

class SetEnvironment extends Command
{
    protected string $signature = 'env:set';

    protected string $description = 'Set the environment.';

    protected function handle(): void
    {
        $this->header('Navigator', 'Set the environment type');

        $env = $this->ask('Select the environment type', Arr::enumValues(Environment::class), $this->app->environment->value);

        if ($this->callSilently('config set WP_ENVIRONMENT_TYPE ' . $env)) {
            $this->success("Environment successfully set as {$env}.");
            return;
        }
    }
}
