<?php

namespace Navigator\Routing\Console\Commands;

use Navigator\Collections\Arr;
use Navigator\Console\Command;
use Navigator\Routing\AjaxRoute;
use Navigator\Routing\RouteInterface;
use Navigator\Routing\Router;

class RoutesList extends Command
{
    protected string $signature = 'routes:list';

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
            ->map(function (RouteInterface $route) {
                return $this->getRouteInformation($route);
            })->filter()
            ->all();
    }

    protected function getRouteInformation(RouteInterface $route): array
    {
        return [
            'Type' => $route instanceof AjaxRoute ? 'AJAX' : 'REST',
            'Method' => Arr::join((array) $route->methods(), '|'),
            'URI'    => $route->uri(),
            'Action' => $route->getActionName(),
        ];
    }
}
