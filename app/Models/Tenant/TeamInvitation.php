<?php

declare(strict_types=1);

namespace App\Models\Tenant;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TeamInvitation extends Model
{
    use HasFactory, HasUuids;

    protected $connection = 'tenant';

    protected $fillable = [
        'email',
        'role',
        'permissions',
        'invited_by',
        'token',
        'accepted_at',
        'expires_at',
    ];

    protected $casts = [
        'role' => UserRole::class,
        'permissions' => 'array',
        'accepted_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // Relationships

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'invited_by');
    }

    // Helper Methods

    public function isPending(): bool
    {
        return $this->accepted_at === null && !$this->isExpired();
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    public function generateToken(): string
    {
        $this->token = Str::random(64);
        $this->save();

        return $this->token;
    }

    // Scopes

    public function scopePending($query)
    {
        return $query->whereNull('accepted_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeExpired($query)
    {
        return $query->whereNull('accepted_at')
            ->where('expires_at', '<=', now());
    }

    public function scopeAccepted($query)
    {
        return $query->whereNotNull('accepted_at');
    }
}
