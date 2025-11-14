<?php

namespace App\Models\Tenant;

use App\Enums\DeploymentStatus;
use App\Enums\UptimeStatus;
use App\Enums\WebsiteEnvironment;
use App\Enums\WebsiteStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Website extends Model
{
    use HasFactory, HasUuids;

    protected $connection = 'tenant';

    protected $fillable = [
        'client_id',
        'project_id',
        'name',
        'url',
        'environment',
        'status',
        'server_provider',
        'server_id',
        'server_ip',
        'repository_provider',
        'repository_url',
        'repository_branch',
        'deployment_method',
        'deployment_status',
        'last_deployed_at',
        'uptime_status',
        'last_checked_at',
        'response_time_ms',
        'lighthouse_performance',
        'lighthouse_accessibility',
        'lighthouse_seo',
        'lighthouse_checked_at',
        'settings',
        'notes',
    ];

    protected $casts = [
        'environment' => WebsiteEnvironment::class,
        'status' => WebsiteStatus::class,
        'deployment_status' => DeploymentStatus::class,
        'uptime_status' => UptimeStatus::class,
        'last_deployed_at' => 'datetime',
        'last_checked_at' => 'datetime',
        'lighthouse_checked_at' => 'datetime',
        'lighthouse_performance' => 'integer',
        'lighthouse_accessibility' => 'integer',
        'lighthouse_seo' => 'integer',
        'response_time_ms' => 'integer',
        'settings' => 'array',
        'archived_at' => 'datetime',
    ];

    // Relationships
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    // Accessors
    public function getIsOnlineAttribute(): bool
    {
        return $this->uptime_status === UptimeStatus::UP;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', WebsiteStatus::ACTIVE->value);
    }

    public function scopeOnline($query)
    {
        return $query->where('uptime_status', UptimeStatus::UP->value);
    }
}
