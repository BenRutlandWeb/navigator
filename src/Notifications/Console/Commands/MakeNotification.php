<?php

namespace Navigator\Notifications\Console\Commands;

use Navigator\Console\GeneratorCommand;

class MakeNotification extends GeneratorCommand
{
    protected string $type = 'Notification';

    protected string $signature = 'make:notification {name : The notification class}
                                      {--force : Overwrite the notification class if it exists}';

    protected string $description = 'Make a notification class.';

    protected function getStub(): string
    {
        return __DIR__ . '/stubs/notification.stub';
    }

    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . '\\Notifications';
    }
}
