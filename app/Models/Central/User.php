<?php

namespace App\Models\Central;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    protected $connection = 'pgsql'; // Central database

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'role',
        'permissions',
        'avatar_url',
        'phone',
        'timezone',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'last_active_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
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
            'two_factor_confirmed_at' => 'datetime',
            'last_active_at' => 'datetime',
        ];
    }

    // Relationships
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    // Helper methods
    public function isOwner(): bool
    {
        return $this->role === UserRole::OWNER;
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, [UserRole::OWNER, UserRole::ADMIN]);
    }

    public function hasPermission(string $permission): bool
    {
        // Owners have all permissions
        if ($this->isOwner()) {
            return true;
        }

        // Check if user has wildcard permission
        if (in_array('*', $this->permissions)) {
            return true;
        }

        // Check exact permission
        if (in_array($permission, $this->permissions)) {
            return true;
        }

        // Check wildcard permission (e.g., 'clients.*' matches 'clients.view')
        $parts = explode('.', $permission);
        if (count($parts) === 2) {
            $wildcardPermission = $parts[0] . '.*';
            if (in_array($wildcardPermission, $this->permissions)) {
                return true;
            }
        }

        return false;
    }
}
