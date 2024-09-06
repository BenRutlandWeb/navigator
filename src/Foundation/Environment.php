<?php

namespace Navigator\Foundation;

enum Environment: string
{
    case LOCAL       = 'local';
    case DEVELOPMENT = 'development';
    case STAGING     = 'staging';
    case PRODUCTION  = 'production';

    public function isLocal(): bool
    {
        return $this === self::LOCAL;
    }

    public function isDevelopment(): bool
    {
        return $this === self::DEVELOPMENT;
    }

    public function isStaging(): bool
    {
        return $this === self::STAGING;
    }

    public function isProduction(): bool
    {
        return $this === self::PRODUCTION;
    }
}
