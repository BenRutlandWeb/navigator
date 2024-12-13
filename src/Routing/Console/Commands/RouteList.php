<?php

namespace Navigator\Routing\Console\Commands;

use Navigator\Collections\Arr;
use Navigator\Console\Command;
use Navigator\Routing\AjaxRoute;
use Navigator\Routing\RestRoute;
use Navigator\Routing\RouteInterface;
use Navigator\Routing\Router;
use Navigator\Routing\StaticRoute;

class RouteList extends Command
{
    protected string $signature = 'route:list';

    protected string $description = 'List the registered routes.';

    protected $headers = ['Type', 'Method', 'URI', 'Action'];

    protected function handle(): void
    {
        if (empty($this->getRoutes())) {
            $this->error('Your application doesn\'t have any routes.');
        } else {
            $this->table($this->headers, $this->getRoutes());
        }
    }

    public function getRoutes()
    {
        return $this->app->get(Router::class)
            ->getRoutes()
            ->map(fn(RouteInterface $route) => $this->getRouteInformation($route))
            ->filter()
            ->all();
    }

    protected function getRouteInformation(RouteInterface $route): array
    {
        return [
            'Type'   => $this->getRouteType($route),
            'Method' => Arr::join((array) $route->methods(), '|'),
            'URI'    => $route->uri(),
            'Action' => $route->getActionName(),
        ];
    }

    public function getRouteType(RouteInterface $route): string
    {
        return match (true) {
            $route instanceof AjaxRoute   => 'AJAX',
            $route instanceof RestRoute   => 'REST',
            $route instanceof StaticRoute => 'Static',
        };
    }
}
