<?php

namespace App\Enums;

enum UserRole: string
{
    case OWNER = 'owner';
    case ADMIN = 'admin';
    case MEMBER = 'member';

    public function label(): string
    {
        return match($this) {
            self::OWNER => 'Owner',
            self::ADMIN => 'Admin',
            self::MEMBER => 'Member',
        };
    }

    public function defaultPermissions(): array
    {
        return match($this) {
            self::OWNER => ['*'], // All permissions
            self::ADMIN => [
                'clients.*',
                'projects.*',
                'websites.*',
                'tickets.*',
                'invoices.view',
                'team.*',
                'settings.view',
            ],
            self::MEMBER => [
                'clients.view',
                'projects.view',
                'projects.update',
                'tasks.*',
                'tickets.view',
                'tickets.create',
                'time_entries.*',
                'team.view',
            ],
        };
    }
}
