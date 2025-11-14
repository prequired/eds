<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Tenant\Websites\CheckWebsiteLighthouseAction;
use App\Enums\WebsiteStatus;
use App\Models\Central\Tenant;
use App\Models\Tenant\Website;
use Illuminate\Console\Command;

class CheckWebsitesLighthouse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websites:check-lighthouse
                            {--tenant= : Check websites for a specific tenant only}
                            {--limit= : Limit the number of websites to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Lighthouse performance scores for all active websites';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting Lighthouse performance checks...');
        $this->warn('Note: This process may take a while as each check takes 30-60 seconds.');

        $tenants = $this->option('tenant')
            ? Tenant::where('id', $this->option('tenant'))->get()
            : Tenant::all();

        $totalChecked = 0;
        $totalScores = [
            'performance' => [],
            'accessibility' => [],
            'seo' => [],
        ];

        foreach ($tenants as $tenant) {
            tenancy()->initialize($tenant);

            $websites = Website::query()
                ->where('status', WebsiteStatus::ACTIVE)
                ->whereNull('archived_at')
                ->when($this->option('limit'), fn($q) => $q->limit((int) $this->option('limit')))
                ->get();

            if ($websites->isEmpty()) {
                $this->comment("No active websites for tenant: {$tenant->id}");
                continue;
            }

            $this->info("Checking {$websites->count()} websites for tenant: {$tenant->id}");

            $bar = $this->output->createProgressBar($websites->count());
            $bar->start();

            foreach ($websites as $website) {
                $action = new CheckWebsiteLighthouseAction();
                $result = $action($website);

                $totalChecked++;

                // Collect scores for averaging
                if ($result['performance'] !== null) {
                    $totalScores['performance'][] = $result['performance'];
                }
                if ($result['accessibility'] !== null) {
                    $totalScores['accessibility'][] = $result['accessibility'];
                }
                if ($result['seo'] !== null) {
                    $totalScores['seo'][] = $result['seo'];
                }

                // Alert on low scores
                if ($result['performance'] !== null && $result['performance'] < 50) {
                    $this->newLine();
                    $this->error("Low Performance Score: {$website->name} ({$result['performance']}/100)");
                }

                $bar->advance();

                // Add delay to avoid rate limiting
                sleep(2);
            }

            $bar->finish();
            $this->newLine(2);

            tenancy()->end();
        }

        $this->newLine();
        $this->info('Lighthouse checks completed!');

        // Calculate and display average scores
        $avgPerformance = !empty($totalScores['performance'])
            ? round(array_sum($totalScores['performance']) / count($totalScores['performance']), 1)
            : 'N/A';

        $avgAccessibility = !empty($totalScores['accessibility'])
            ? round(array_sum($totalScores['accessibility']) / count($totalScores['accessibility']), 1)
            : 'N/A';

        $avgSeo = !empty($totalScores['seo'])
            ? round(array_sum($totalScores['seo']) / count($totalScores['seo']), 1)
            : 'N/A';

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Checked', $totalChecked],
                ['Avg Performance', $avgPerformance],
                ['Avg Accessibility', $avgAccessibility],
                ['Avg SEO', $avgSeo],
            ]
        );

        return Command::SUCCESS;
    }
}
