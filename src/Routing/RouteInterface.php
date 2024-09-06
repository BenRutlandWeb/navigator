<?php

namespace Navigator\Routing;

use Navigator\Http\Request;

interface RouteInterface
{
    public function events(): array;

    public function dispatch(Request $request): void;

    public function methods(): array|string;

    public function uri(): string;

    public function getActionName(): string;
}
