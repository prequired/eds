<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Websites;

use App\Enums\UptimeStatus;
use App\Events\Tenant\WebsiteUptimeChanged;
use App\Models\Tenant\UptimeCheck;
use App\Models\Tenant\Website;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CheckWebsiteUptimeAction
{
    /**
     * Check the uptime status of a website and update its metrics.
     *
     * @param Website $website
     * @return array{status: UptimeStatus, response_time_ms: int|null, status_code: int|null}
     */
    public function __invoke(Website $website): array
    {
        $previousStatus = $website->uptime_status;
        $startTime = microtime(true);
        $errorMessage = null;

        try {
            // Make HTTP request with timeout
            $response = Http::timeout(10)
                ->retry(2, 100)
                ->get($website->url);

            $endTime = microtime(true);
            $responseTimeMs = (int) (($endTime - $startTime) * 1000);

            // Determine uptime status based on response code
            $uptimeStatus = $response->successful()
                ? UptimeStatus::UP
                : UptimeStatus::DOWN;

            $statusCode = $response->status();

            if (!$response->successful()) {
                $errorMessage = "HTTP {$statusCode} response";
            }

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $responseTimeMs = (int) (($endTime - $startTime) * 1000);
            $uptimeStatus = UptimeStatus::DOWN;
            $statusCode = null;
            $errorMessage = $e->getMessage();
        }

        // Update website metrics and create uptime check record
        DB::transaction(function () use ($website, $uptimeStatus, $responseTimeMs, $statusCode, $errorMessage, $previousStatus) {
            // Update website
            $website->update([
                'uptime_status' => $uptimeStatus,
                'response_time_ms' => $responseTimeMs,
                'last_checked_at' => now(),
            ]);

            // Create uptime check record for history
            UptimeCheck::create([
                'website_id' => $website->id,
                'status' => $uptimeStatus,
                'response_time_ms' => $responseTimeMs,
                'status_code' => $statusCode,
                'error_message' => $errorMessage,
                'checked_at' => now(),
            ]);

            // Dispatch event if status changed
            if ($previousStatus !== $uptimeStatus) {
                WebsiteUptimeChanged::dispatch($website, $previousStatus, $uptimeStatus);
            }
        });

        return [
            'status' => $uptimeStatus,
            'response_time_ms' => $responseTimeMs,
            'status_code' => $statusCode,
            'error_message' => $errorMessage,
        ];
    }
}
