<?php

namespace Navigator\Mail;

use Navigator\Foundation\ServiceProvider;
use Navigator\Mail\Console\Commands\MakeMail;

class MailServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(Mailer::class, fn() => new Mailer());
    }

    public function boot(): void
    {
        $this->commands([
            MakeMail::class,
        ]);
    }
}
