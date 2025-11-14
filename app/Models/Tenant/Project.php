<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory, HasUuids;

    protected $connection = 'tenant';

    protected $fillable = [
        'client_id',
        'name',
        'description',
        'status',
        'priority',
        'budget_cents',
        'currency',
        'estimated_hours',
        'actual_hours',
        'starts_at',
        'due_at',
        'completed_at',
        'billable',
        'hourly_rate_cents',
    ];

    protected $casts = [
        'budget_cents' => 'integer',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'starts_at' => 'date',
        'due_at' => 'date',
        'completed_at' => 'datetime',
        'billable' => 'boolean',
        'hourly_rate_cents' => 'integer',
        'archived_at' => 'datetime',
    ];

    // Relationships
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function websites(): HasMany
    {
        return $this->hasMany(Website::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
