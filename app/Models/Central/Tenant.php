<?php

namespace App\Models\Central;

use App\Enums\PlanType;
use App\Enums\TenantStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains, HasUuids;

    protected $connection = 'pgsql'; // Central database

    protected $fillable = [
        'company_name',
        'subdomain',
        'owner_email',
        'plan',
        'status',
        'trial_ends_at',
        'client_limit',
        'website_limit',
        'storage_limit_gb',
        'bandwidth_limit_gb',
        'settings',
        'data',
    ];

    protected $casts = [
        'plan' => PlanType::class,
        'status' => TenantStatus::class,
        'trial_ends_at' => 'datetime',
        'settings' => 'array',
        'data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'company_name',
            'subdomain',
            'owner_email',
            'plan',
            'status',
            'trial_ends_at',
            'client_limit',
            'website_limit',
            'storage_limit_gb',
            'bandwidth_limit_gb',
            'settings',
            'data',
        ];
    }

    // Relationships
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_email', 'email');
    }

    // Helper methods
    public function isOnTrial(): bool
    {
        return $this->status === TenantStatus::TRIAL
            && $this->trial_ends_at?->isFuture();
    }

    public function isActive(): bool
    {
        return $this->status === TenantStatus::ACTIVE;
    }

    public function canAddClient(): bool
    {
        if ($this->client_limit === null) {
            return true; // Unlimited
        }

        return $this->run(function () {
            return \App\Models\Tenant\Client::count() < $this->client_limit;
        });
    }

    public function canAddWebsite(): bool
    {
        if ($this->website_limit === null) {
            return true; // Unlimited
        }

        return $this->run(function () {
            return \App\Models\Tenant\Website::count() < $this->website_limit;
        });
    }

    public function getBrandingAttribute(): array
    {
        return $this->settings['branding'] ?? [
            'logo_url' => null,
            'primary_color' => '#3B82F6',
            'secondary_color' => '#10B981',
        ];
    }

    public function getFeature(string $feature): bool
    {
        return $this->settings['features'][$feature] ?? false;
    }
}
