<?php

namespace Navigator\Mail\Console\Commands;

use Navigator\Console\GeneratorCommand;

class MakeMail extends GeneratorCommand
{
    protected string $type = 'Mail';

    protected string $signature = 'make:mail {name : The mail class}
                                      {--force : Overwrite the mail class if it exists}';

    protected string $description = 'Make a mailable class.';

    protected function getStub(): string
    {
        return __DIR__ . '/stubs/mail.stub';
    }

    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . '\\Mail';
    }
}
