<?php

declare(strict_types=1);

namespace App\Models\Tenant;

use App\Enums\UptimeStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UptimeCheck extends Model
{
    use HasUuids;

    protected $fillable = [
        'website_id',
        'status',
        'response_time_ms',
        'status_code',
        'error_message',
        'checked_at',
    ];

    protected $casts = [
        'status' => UptimeStatus::class,
        'response_time_ms' => 'integer',
        'status_code' => 'integer',
        'checked_at' => 'datetime',
    ];

    /**
     * Get the website this check belongs to.
     */
    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }

    /**
     * Check if the uptime check was successful.
     */
    public function wasSuccessful(): bool
    {
        return $this->status === UptimeStatus::UP;
    }

    /**
     * Check if the uptime check failed.
     */
    public function failed(): bool
    {
        return $this->status === UptimeStatus::DOWN;
    }
}
