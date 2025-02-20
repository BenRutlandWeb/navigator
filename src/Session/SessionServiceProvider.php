<?php

namespace Navigator\Session;

use Navigator\Database\Connection;
use Navigator\Filesystem\Filesystem;
use Navigator\Foundation\Application;
use Navigator\Foundation\ServiceProvider;
use Navigator\Http\Request;
use Navigator\Session\Console\Commands\SessionTables;
use Navigator\Session\Handlers\ArraySessionHandler;
use Navigator\Session\Handlers\DatabaseSessionHandler;
use Navigator\Session\Handlers\FileSessionHandler;
use Navigator\Session\Handlers\NullSessionHandler;

class SessionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(NullSessionHandler::class, fn() => new NullSessionHandler());
        $this->app->singleton(ArraySessionHandler::class, fn() => new ArraySessionHandler());
        $this->app->singleton(DatabaseSessionHandler::class, function (Application $app) {
            return new DatabaseSessionHandler($app->get(Connection::class));
        });
        $this->app->singleton(FileSessionHandler::class, function (Application $app) {
            return new FileSessionHandler($app->get(Filesystem::class), wp_get_upload_dir()['basedir'] . '/sessions', 120);
        });

        $this->app->singleton(SessionManager::class, function (Application $app) {

            $handlers = [
                'database' => DatabaseSessionHandler::class,
                'array'    => ArraySessionHandler::class,
                'files'    => FileSessionHandler::class,
                'null'     => NullSessionHandler::class,
            ];

            $handler = $handlers[$app->env('SESSION_DRIVER')] ?? FileSessionHandler::class;

            return new SessionManager($app->get($handler));
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
