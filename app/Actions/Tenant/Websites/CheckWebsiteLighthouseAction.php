<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Websites;

use App\Events\Tenant\WebsiteLighthouseUpdated;
use App\Models\Tenant\Website;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CheckWebsiteLighthouseAction
{
    /**
     * Check Lighthouse performance metrics for a website using Google PageSpeed Insights API.
     *
     * @param Website $website
     * @return array{performance: int|null, accessibility: int|null, seo: int|null}
     */
    public function __invoke(Website $website): array
    {
        try {
            // Get PageSpeed Insights API key from config (optional but recommended)
            $apiKey = config('services.pagespeed.api_key');

            // Build API URL
            $url = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';
            $params = [
                'url' => $website->url,
                'category' => ['performance', 'accessibility', 'seo'],
            ];

            if ($apiKey) {
                $params['key'] = $apiKey;
            }

            // Make API request (with longer timeout for Lighthouse)
            $response = Http::timeout(60)
                ->get($url, $params);

            if (!$response->successful()) {
                throw new \Exception("PageSpeed API request failed: {$response->status()}");
            }

            $data = $response->json();

            // Extract Lighthouse scores (0-100)
            $performance = isset($data['lighthouseResult']['categories']['performance']['score'])
                ? (int) ($data['lighthouseResult']['categories']['performance']['score'] * 100)
                : null;

            $accessibility = isset($data['lighthouseResult']['categories']['accessibility']['score'])
                ? (int) ($data['lighthouseResult']['categories']['accessibility']['score'] * 100)
                : null;

            $seo = isset($data['lighthouseResult']['categories']['seo']['score'])
                ? (int) ($data['lighthouseResult']['categories']['seo']['score'] * 100)
                : null;

        } catch (\Exception $e) {
            // Log error but don't fail - Lighthouse checks are not critical
            \Log::warning("Lighthouse check failed for website {$website->id}: {$e->getMessage()}");

            return [
                'performance' => null,
                'accessibility' => null,
                'seo' => null,
            ];
        }

        // Update website Lighthouse metrics
        DB::transaction(function () use ($website, $performance, $accessibility, $seo) {
            $previousScores = [
                'performance' => $website->lighthouse_performance,
                'accessibility' => $website->lighthouse_accessibility,
                'seo' => $website->lighthouse_seo,
            ];

            $website->update([
                'lighthouse_performance' => $performance,
                'lighthouse_accessibility' => $accessibility,
                'lighthouse_seo' => $seo,
                'lighthouse_checked_at' => now(),
            ]);

            // Dispatch event with scores
            WebsiteLighthouseUpdated::dispatch($website, $previousScores, [
                'performance' => $performance,
                'accessibility' => $accessibility,
                'seo' => $seo,
            ]);
        });

        return [
            'performance' => $performance,
            'accessibility' => $accessibility,
            'seo' => $seo,
        ];
    }
}
