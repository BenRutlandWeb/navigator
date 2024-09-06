<?php

namespace Navigator\Pagination;

use Navigator\Foundation\ServiceProvider;
use Navigator\Http\Request;
use Navigator\View\ViewFactory;

class PaginationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Paginator::viewFactoryResolver(function () {
            return $this->app->get(ViewFactory::class);
        });

        Paginator::currentPathResolver(function () {
            return $this->app->get(Request::class)->url();
        });

        Paginator::currentPageResolver(function (string $pageName = 'page') {
            $page = $this->app->get(Request::class)->input($pageName, 1);

            if (filter_var($page, FILTER_VALIDATE_INT) !== false && (int) $page >= 1) {
                return (int) $page;
            }

            return 1;
        });
    }

    public function boot(): void
    {
        //
    }
}
