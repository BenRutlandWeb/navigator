<?php

namespace Navigator\Contracts;

interface ServiceProviderInterface
{
    public function register(): void;

    public function boot(): void;
}
