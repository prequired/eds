<?php

declare(strict_types=1);

namespace App\Enums;

enum DeploymentStatus: string
{
    case IDLE = 'idle';
    case DEPLOYING = 'deploying';
    case SUCCESS = 'success';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::IDLE => 'Idle',
            self::DEPLOYING => 'Deploying',
            self::SUCCESS => 'Success',
            self::FAILED => 'Failed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::IDLE => 'gray',
            self::DEPLOYING => 'blue',
            self::SUCCESS => 'green',
            self::FAILED => 'red',
        };
    }
}
