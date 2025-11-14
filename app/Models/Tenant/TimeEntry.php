<?php

declare(strict_types=1);

namespace App\Models\Tenant;

use App\Enums\TimeEntryStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeEntry extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $connection = 'tenant';

    protected $fillable = [
        'user_id',
        'project_id',
        'client_id',
        'description',
        'start_time',
        'end_time',
        'duration',
        'billable',
        'hourly_rate',
        'status',
        'invoice_id',
        'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'duration' => 'integer',
        'billable' => 'boolean',
        'hourly_rate' => 'decimal:2',
        'status' => TimeEntryStatus::class,
    ];

    protected static function booted(): void
    {
        static::saving(function (TimeEntry $entry) {
            // Auto-calculate duration if end_time is set
            if ($entry->end_time && $entry->start_time) {
                $entry->duration = $entry->end_time->diffInSeconds($entry->start_time);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function isRunning(): bool
    {
        return $this->end_time === null && $this->start_time !== null;
    }

    public function stop(): void
    {
        if ($this->isRunning()) {
            $this->end_time = now();
            $this->save();
        }
    }

    public function getDurationInHoursAttribute(): float
    {
        return round($this->duration / 3600, 2);
    }

    public function getFormattedDurationAttribute(): string
    {
        $hours = floor($this->duration / 3600);
        $minutes = floor(($this->duration % 3600) / 60);
        $seconds = $this->duration % 60;

        if ($hours > 0) {
            return sprintf('%dh %02dm', $hours, $minutes);
        } elseif ($minutes > 0) {
            return sprintf('%dm %02ds', $minutes, $seconds);
        } else {
            return sprintf('%ds', $seconds);
        }
    }

    public function getTotalAmountAttribute(): float
    {
        if (!$this->billable || !$this->hourly_rate) {
            return 0;
        }

        return round($this->duration_in_hours * (float) $this->hourly_rate, 2);
    }

    public function scopeRunning($query)
    {
        return $query->whereNull('end_time')->whereNotNull('start_time');
    }

    public function scopeCompleted($query)
    {
        return $query->whereNotNull('end_time');
    }

    public function scopeBillable($query)
    {
        return $query->where('billable', true);
    }

    public function scopeNotInvoiced($query)
    {
        return $query->whereNull('invoice_id')
            ->where('status', '!=', TimeEntryStatus::INVOICED);
    }

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForProject($query, string $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeForClient($query, string $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_time', [$startDate, $endDate]);
    }
}
