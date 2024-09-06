<?php

namespace Navigator\Foundation\Console\Commands;

use Navigator\Collections\Arr;
use Navigator\Console\Command;
use Navigator\Foundation\Environment;

class SetEnvironment extends Command
{
    protected string $signature = 'env:set { type : The environment type }';

    protected string $description = 'Set the environment.';

    protected function handle(): void
    {
        $env = $this->argument('type');

        if (Environment::tryFrom($env)) {
            $this->call('config set WP_ENVIRONMENT_TYPE ' . $env);
            return;
        }

        $types = join(', ', Arr::map(Environment::cases(), fn ($type) => $type->value));

        $this->error("Valid environment types are: [{$types}]");
    }
}
