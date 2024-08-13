<?php

namespace Navigator\Routing;

use Navigator\Events\Dispatcher;
use Navigator\Foundation\Application;
use Navigator\Foundation\ServiceProvider;
use Navigator\Http\Request;

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
    }

    public function registerRoutes(Router $router): void
    {
        //
    }
}
