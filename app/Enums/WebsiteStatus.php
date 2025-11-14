<?php

declare(strict_types=1);

namespace App\Enums;

enum WebsiteStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case MAINTENANCE = 'maintenance';
    case SUSPENDED = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::MAINTENANCE => 'Maintenance',
            self::SUSPENDED => 'Suspended',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'green',
            self::INACTIVE => 'gray',
            self::MAINTENANCE => 'yellow',
            self::SUSPENDED => 'red',
        };
    }
}
