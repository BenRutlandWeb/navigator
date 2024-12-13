<?php

namespace Navigator\Routing;

use Navigator\Events\Dispatcher;
use Navigator\Foundation\ServiceProvider;
use Navigator\Http\Request;
use Navigator\Routing\Console\Commands\MakeController;
use Navigator\Routing\Console\Commands\RouteClear;
use Navigator\Routing\Console\Commands\RouteList;

class RoutingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->app->get(Dispatcher::class)->listen('query_vars', function (array $args) {
            $args[] = 'navigator';

            return $args;
        });

        $this->registerRoutes($router = $this->app->get(Router::class));

        $router->dispatch($this->app->get(Request::class));

        $this->commands([
            MakeController::class,
            RouteClear::class,
            RouteList::class,
        ]);
    }

    public function registerRoutes(Router $router): void
    {
        //
    }
}
