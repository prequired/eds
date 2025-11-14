<?php

declare(strict_types=1);

namespace App\Enums;

enum UptimeStatus: string
{
    case UP = 'up';
    case DOWN = 'down';
    case UNKNOWN = 'unknown';

    public function label(): string
    {
        return match ($this) {
            self::UP => 'Up',
            self::DOWN => 'Down',
            self::UNKNOWN => 'Unknown',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::UP => 'green',
            self::DOWN => 'red',
            self::UNKNOWN => 'gray',
        };
    }
}
