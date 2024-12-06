<?php

namespace Navigator\Validation;

use Navigator\Foundation\Application;
use Navigator\Foundation\ServiceProvider;
use Navigator\Http\Request;
use Navigator\Validation\Console\Commands\MakeRule;
use Navigator\Validation\Rules\CurrentPassword;
use Navigator\Validation\Rules\EmailExists;
use Navigator\Validation\Rules\Enum;
use Navigator\Validation\Rules\Rule;
use Navigator\Validation\Rules\UniqueEmail;
use Navigator\Validation\Rules\UniqueUsername;
use Navigator\Validation\Rules\UsernameExists;
use Rakit\Validation\Validator;

class ValidationServiceProvider extends ServiceProvider
{
    /** @var array<string, Rule> */
    protected array $internalValidators = [
        'current_password' => CurrentPassword::class,
        'email_exists'     => EmailExists::class,
        'enum'             => Enum::class,
        'username_exists'  => UsernameExists::class,
        'unique_email'     => UniqueEmail::class,
        'unique_username'  => UniqueUsername::class,
    ];

    public function register(): void
    {
        $this->app->singleton(Validator::class, function () {
            return $this->registerValidators(new Validator());
        });

        $this->app->singleton(ValidationFactory::class, fn(Application $app) => new ValidationFactory(
            $app->get(Validator::class)
        ));

        $this->app->extend(Request::class, function (Request $request, Application $app) {
            $request->setValidatorResolver(fn() => $app->get(ValidationFactory::class));

            return $request;
        });
    }

    public function registerValidators(Validator $validator): Validator
    {
        foreach ($this->internalValidators as $key => $v) {
            $validator->addValidator($key, new $v());
        }

        return $validator;
    }

    public function boot(): void
    {
        $this->commands([
            MakeRule::class,
        ]);
    }
}
