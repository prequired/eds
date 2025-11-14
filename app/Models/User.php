<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, HasUuids, Notifiable;

    protected $connection = 'tenant';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'permissions',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'permissions' => 'array',
        ];
    }

    // Role & Permission Helpers

    public function isOwner(): bool
    {
        return $this->role === UserRole::OWNER;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isMember(): bool
    {
        return $this->role === UserRole::MEMBER;
    }

    public function hasPermission(string $permission): bool
    {
        // Owner has all permissions
        if ($this->isOwner()) {
            return true;
        }

        // Check custom permissions first
        if ($this->permissions !== null && in_array($permission, $this->permissions)) {
            return true;
        }

        // Check role default permissions
        $defaultPermissions = $this->role->defaultPermissions();

        // Check for wildcard permissions (e.g., 'clients.*')
        foreach ($defaultPermissions as $defaultPermission) {
            if ($defaultPermission === '*') {
                return true;
            }

            if (str_ends_with($defaultPermission, '.*')) {
                $prefix = substr($defaultPermission, 0, -2);
                if (str_starts_with($permission, $prefix . '.')) {
                    return true;
                }
            }

            if ($defaultPermission === $permission) {
                return true;
            }
        }

        return false;
    }

    public function can($ability, $arguments = []): bool
    {
        // First check Laravel's built-in authorization
        $can = parent::can($ability, $arguments);

        if ($can) {
            return true;
        }

        // Fall back to our permission system
        return $this->hasPermission($ability);
    }
}
