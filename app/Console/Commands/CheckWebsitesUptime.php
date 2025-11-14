<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Tenant\Websites\CheckWebsiteUptimeAction;
use App\Enums\WebsiteStatus;
use App\Models\Central\Tenant;
use App\Models\Tenant\Website;
use Illuminate\Console\Command;

class CheckWebsitesUptime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websites:check-uptime
                            {--tenant= : Check websites for a specific tenant only}
                            {--limit= : Limit the number of websites to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the uptime status of all active websites across all tenants';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting website uptime checks...');

        $tenants = $this->option('tenant')
            ? Tenant::where('id', $this->option('tenant'))->get()
            : Tenant::all();

        $totalChecked = 0;
        $totalUp = 0;
        $totalDown = 0;

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
                $action = new CheckWebsiteUptimeAction();
                $result = $action($website);

                $totalChecked++;

                if ($result['status']->value === 'up') {
                    $totalUp++;
                } else {
                    $totalDown++;
                    $this->newLine();
                    $this->error("Website DOWN: {$website->name} ({$website->url})");
                    if ($result['error_message']) {
                        $this->line("  Error: {$result['error_message']}");
                    }
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);

            tenancy()->end();
        }

        $this->newLine();
        $this->info('Uptime checks completed!');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Checked', $totalChecked],
                ['Online (UP)', $totalUp],
                ['Offline (DOWN)', $totalDown],
            ]
        );

        return Command::SUCCESS;
    }
}
