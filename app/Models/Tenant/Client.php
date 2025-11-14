<?php

namespace App\Models\Tenant;

use App\Enums\ClientStatus;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Client extends Model implements AuthenticatableContract
{
    use HasFactory, HasUuids, Authenticatable, Notifiable;

    protected $connection = 'tenant'; // Tenant database

    protected $fillable = [
        'name',
        'company',
        'email',
        'phone',
        'website',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'billing_email',
        'monthly_retainer_cents',
        'currency',
        'payment_terms',
        'status',
        'portal_enabled',
        'portal_password',
        'settings',
        'notes',
        'tags',
    ];

    protected $casts = [
        'monthly_retainer_cents' => 'integer',
        'payment_terms' => 'integer',
        'status' => ClientStatus::class,
        'portal_enabled' => 'boolean',
        'settings' => 'array',
        'tags' => 'array',
        'archived_at' => 'datetime',
        'last_portal_login' => 'datetime',
    ];

    protected $hidden = [
        'portal_password',
        'remember_token',
    ];

    // Relationships
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function websites(): HasMany
    {
        return $this->hasMany(Website::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    // Accessors
    public function getMonthlyRetainerAttribute(): string
    {
        return '$' . number_format($this->monthly_retainer_cents / 100, 2);
    }

    /**
     * Get the password for the client portal.
     */
    public function getAuthPassword(): string
    {
        return $this->portal_password;
    }

    /**
     * Check if client can access portal.
     */
    public function canAccessPortal(): bool
    {
        return $this->portal_enabled
            && $this->status === ClientStatus::ACTIVE
            && !empty($this->portal_password);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', ClientStatus::ACTIVE);
    }

    public function scopePortalEnabled($query)
    {
        return $query->where('portal_enabled', true)
            ->where('status', ClientStatus::ACTIVE)
            ->whereNotNull('portal_password');
    }
}
