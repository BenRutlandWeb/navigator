<?php

namespace Navigator\Auth;

use Navigator\Foundation\Application;
use Navigator\Foundation\ServiceProvider;
use Navigator\Http\Request;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Auth::class, fn() => new Auth);

        $this->app->extend(Request::class, function (Request $request, Application $app) {
            $request->setUserResolver(fn() => $app->get(Auth::class)->user());

            return $request;
        });
    }

    public function boot(): void
    {
        //
    }
}
