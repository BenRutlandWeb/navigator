<?php

namespace Navigator\Console;

use Navigator\Foundation\Application;

class ConsoleFactory
{
    public function __construct(protected Application $app)
    {
        //
    }

    /** @param class-string<Command> $command */
    public function make(string $command): void
    {
        (new $command($this->app))->register();
    }
}
