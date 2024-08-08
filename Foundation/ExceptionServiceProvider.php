<?php

namespace Navigator\Foundation;

use Navigator\Foundation\Application;
use Navigator\Foundation\ServiceProvider;
use Navigator\Http\Request;
use Whoops\Handler\HandlerInterface;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Whoops\RunInterface;

class ExceptionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(HandlerInterface::class, function (Application $app) {
            return $app->get(Request::class)->expectsJson()
                ? new JsonResponseHandler
                : new PrettyPageHandler;
        });

        $this->app->singleton(RunInterface::class, function (Application $app) {
            $handler = $app->get(HandlerInterface::class);

            $whoops = new Run();
            $whoops->allowQuit(false);
            $whoops->writeToOutput(false);

            return $whoops->pushHandler($handler);
        });
    }

    public function boot(): void
    {
        //
    }
}
