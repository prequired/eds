<?php

namespace App\Models\Tenant;

use App\Enums\ClientStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory, HasUuids;

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
        'settings',
        'notes',
        'tags',
    ];

    protected $casts = [
        'monthly_retainer_cents' => 'integer',
        'payment_terms' => 'integer',
        'status' => ClientStatus::class,
        'settings' => 'array',
        'tags' => 'array',
        'archived_at' => 'datetime',
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

    // Accessors
    public function getMonthlyRetainerAttribute(): string
    {
        return '$' . number_format($this->monthly_retainer_cents / 100, 2);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', ClientStatus::ACTIVE);
    }
}
