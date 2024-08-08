<?php

namespace Navigator\Auth;

use Navigator\Foundation\Application;
use Navigator\Foundation\ServiceProvider;
use Navigator\Http\Request;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Auth::class, fn () => new Auth);

        $this->app->rebinding(Request::class, function (Application $app, Request $request) {
            $request->setUserResolver(fn () => $app->get(Auth::class)->user());
        });
    }

    public function boot(): void
    {
        //
    }
}
