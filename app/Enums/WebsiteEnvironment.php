<?php

declare(strict_types=1);

namespace App\Enums;

enum WebsiteEnvironment: string
{
    case PRODUCTION = 'production';
    case STAGING = 'staging';
    case DEVELOPMENT = 'development';

    public function label(): string
    {
        return match ($this) {
            self::PRODUCTION => 'Production',
            self::STAGING => 'Staging',
            self::DEVELOPMENT => 'Development',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PRODUCTION => 'red',
            self::STAGING => 'yellow',
            self::DEVELOPMENT => 'blue',
        };
    }
}
