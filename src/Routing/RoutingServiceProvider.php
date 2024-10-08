<?php

namespace Navigator\Routing;

use Navigator\Foundation\ServiceProvider;
use Navigator\Http\Request;
use Navigator\Routing\Console\Commands\MakeController;
use Navigator\Routing\Console\Commands\RoutesList;

class RoutingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->registerRoutes($router = $this->app->get(Router::class));

        $router->dispatch($this->app->get(Request::class));

        $this->commands([
            MakeController::class,
            RoutesList::class,
        ]);
    }

    public function registerRoutes(Router $router): void
    {
        //
    }
}
