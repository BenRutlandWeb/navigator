<?php

namespace Navigator\Queue\Console\Commands;

use Navigator\Console\GeneratorCommand;

class MakeJob extends GeneratorCommand
{
    protected string $type = 'Job';

    protected string $signature = 'make:job {name : The job class}
                                     {--force : Overwrite the job if it exists}';

    protected string $description = 'Make a job class.';

    protected function getStub(): string
    {
        return __DIR__ . '/stubs/job.stub';
    }

    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . '\Jobs';
    }
}
