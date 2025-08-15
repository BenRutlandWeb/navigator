<?php

namespace Navigator\Session;

use Navigator\Database\Connection;
use Navigator\Foundation\Application;
use Navigator\Foundation\ServiceProvider;
use Navigator\Http\Request;
use Navigator\Session\Console\Commands\SessionTables;
use Navigator\Session\Handlers\DatabaseSessionHandler;

class SessionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(DatabaseSessionHandler::class, function (Application $app) {
            return new DatabaseSessionHandler($app->get(Connection::class));
        });

        $this->app->singleton(SessionManager::class, function (Application $app) {
            return new SessionManager($app->get(DatabaseSessionHandler::class));
        });

        $this->app->singleton(Session::class, fn() => new Session());

        $this->app->extend(Request::class, function (Request $request, Application $app) {
            $request->setSessionResolver(fn() => $app->get(Session::class));

            return $request;
        });
    }

    public function boot(): void
    {
        $this->app->get(SessionManager::class)->start();

        $this->commands([
            SessionTables::class,
        ]);
    }
}
