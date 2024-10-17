<?php

namespace Navigator\Mail;

use Navigator\Events\Dispatcher;
use Navigator\Foundation\Application;
use Navigator\Foundation\ServiceProvider;
use Navigator\Mail\Console\Commands\MakeMail;
use Navigator\View\ViewFactory;

class MailServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(Mailer::class, function (Application $app) {
            return new Mailer($app->get(Dispatcher::class));
        });
    }

    public function boot(): void
    {
        $this->commands([
            MakeMail::class,
        ]);

        $this->app->get(Dispatcher::class)
            ->listen('navigator_mailer_content', function (string $content): string {
                return $this->app->get(ViewFactory::class)->file(
                    __DIR__ . '/resources/views/template.php',
                    ['slot' => $content]
                );
            });
    }
}
