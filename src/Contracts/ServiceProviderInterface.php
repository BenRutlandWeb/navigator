<?php

namespace Navigator\Contracts;

interface ServiceProviderInterface
{
    public function register(): void;

    public function boot(): void;

    /** @return array<string, string> */
    public static function getPublishables(string $tag): array;
}
