<?php

declare(strict_types=1);

namespace App\Livewire\Tenant\TimeEntry;

use App\Enums\TimeEntryStatus;
use App\Models\Tenant\Client;
use App\Models\Tenant\Project;
use App\Models\Tenant\TimeEntry;
use App\Models\Tenant\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Component;

class TimeReports extends Component
{
    #[Url(as: 'report')]
    public string $reportType = 'summary';

    #[Url(as: 'from')]
    public string $dateFrom = '';

    #[Url(as: 'to')]
    public string $dateTo = '';

    #[Url(as: 'project')]
    public string $projectFilter = 'all';

    #[Url(as: 'client')]
    public string $clientFilter = 'all';

    #[Url(as: 'user')]
    public string $userFilter = 'all';

    #[Url(as: 'billable')]
    public string $billableFilter = 'all';

    #[Url(as: 'status')]
    public string $statusFilter = 'all';

    public function mount(): void
    {
        if (empty($this->dateFrom)) {
            $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        }
        if (empty($this->dateTo)) {
            $this->dateTo = now()->endOfMonth()->format('Y-m-d');
        }
    }

    public function setQuickRange(string $range): void
    {
        match ($range) {
            'today' => [
                $this->dateFrom = now()->format('Y-m-d'),
                $this->dateTo = now()->format('Y-m-d'),
            ],
            'yesterday' => [
                $this->dateFrom = now()->subDay()->format('Y-m-d'),
                $this->dateTo = now()->subDay()->format('Y-m-d'),
            ],
            'this_week' => [
                $this->dateFrom = now()->startOfWeek()->format('Y-m-d'),
                $this->dateTo = now()->endOfWeek()->format('Y-m-d'),
            ],
            'last_week' => [
                $this->dateFrom = now()->subWeek()->startOfWeek()->format('Y-m-d'),
                $this->dateTo = now()->subWeek()->endOfWeek()->format('Y-m-d'),
            ],
            'this_month' => [
                $this->dateFrom = now()->startOfMonth()->format('Y-m-d'),
                $this->dateTo = now()->endOfMonth()->format('Y-m-d'),
            ],
            'last_month' => [
                $this->dateFrom = now()->subMonth()->startOfMonth()->format('Y-m-d'),
                $this->dateTo = now()->subMonth()->endOfMonth()->format('Y-m-d'),
            ],
            'this_year' => [
                $this->dateFrom = now()->startOfYear()->format('Y-m-d'),
                $this->dateTo = now()->endOfYear()->format('Y-m-d'),
            ],
            default => null,
        };
    }

    protected function getBaseQuery()
    {
        $query = TimeEntry::with(['user', 'project', 'client'])
            ->when($this->dateFrom, function ($q) {
                $q->whereDate('start_time', '>=', Carbon::parse($this->dateFrom));
            })
            ->when($this->dateTo, function ($q) {
                $q->whereDate('start_time', '<=', Carbon::parse($this->dateTo));
            })
            ->when($this->projectFilter !== 'all', function ($q) {
                $q->where('project_id', $this->projectFilter);
            })
            ->when($this->clientFilter !== 'all', function ($q) {
                $q->where('client_id', $this->clientFilter);
            })
            ->when($this->userFilter !== 'all', function ($q) {
                $q->where('user_id', $this->userFilter);
            })
            ->when($this->billableFilter !== 'all', function ($q) {
                $q->where('billable', $this->billableFilter === 'yes');
            })
            ->when($this->statusFilter !== 'all', function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->where('end_time', '!=', null); // Only completed entries

        // Non-admin users only see their own entries
        if (!auth()->user()->isOwner() && !auth()->user()->isAdmin()) {
            $query->forUser(auth()->id());
        }

        return $query;
    }

    public function getSummaryData(): array
    {
        $entries = $this->getBaseQuery()->get();

        $totalHours = $entries->sum('duration_in_hours');
        $billableHours = $entries->where('billable', true)->sum('duration_in_hours');
        $nonBillableHours = $entries->where('billable', false)->sum('duration_in_hours');
        $totalAmount = $entries->sum('total_amount');

        $byStatus = $entries->groupBy('status')->map(function ($group) {
            return [
                'count' => $group->count(),
                'hours' => $group->sum('duration_in_hours'),
                'amount' => $group->sum('total_amount'),
            ];
        });

        return [
            'total_entries' => $entries->count(),
            'total_hours' => round($totalHours, 2),
            'billable_hours' => round($billableHours, 2),
            'non_billable_hours' => round($nonBillableHours, 2),
            'total_amount' => round($totalAmount, 2),
            'by_status' => $byStatus,
        ];
    }

    public function getProjectData(): Collection
    {
        $entries = $this->getBaseQuery()->get();

        return $entries->groupBy('project_id')->map(function ($group) {
            $project = $group->first()->project;
            return [
                'project_id' => $project?->id,
                'project_name' => $project?->name ?? 'No Project',
                'entries_count' => $group->count(),
                'total_hours' => round($group->sum('duration_in_hours'), 2),
                'billable_hours' => round($group->where('billable', true)->sum('duration_in_hours'), 2),
                'total_amount' => round($group->sum('total_amount'), 2),
            ];
        })->sortByDesc('total_hours')->values();
    }

    public function getClientData(): Collection
    {
        $entries = $this->getBaseQuery()->get();

        return $entries->groupBy('client_id')->map(function ($group) {
            $client = $group->first()->client;
            return [
                'client_id' => $client?->id,
                'client_name' => $client?->name ?? 'No Client',
                'entries_count' => $group->count(),
                'total_hours' => round($group->sum('duration_in_hours'), 2),
                'billable_hours' => round($group->where('billable', true)->sum('duration_in_hours'), 2),
                'total_amount' => round($group->sum('total_amount'), 2),
            ];
        })->sortByDesc('total_hours')->values();
    }

    public function getUserData(): Collection
    {
        $entries = $this->getBaseQuery()->get();

        return $entries->groupBy('user_id')->map(function ($group) {
            $user = $group->first()->user;
            return [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'entries_count' => $group->count(),
                'total_hours' => round($group->sum('duration_in_hours'), 2),
                'billable_hours' => round($group->where('billable', true)->sum('duration_in_hours'), 2),
                'total_amount' => round($group->sum('total_amount'), 2),
            ];
        })->sortByDesc('total_hours')->values();
    }

    public function getDetailedData(): Collection
    {
        return $this->getBaseQuery()
            ->orderBy('start_time', 'desc')
            ->get();
    }

    public function exportCsv(): void
    {
        $data = match ($this->reportType) {
            'summary' => $this->getSummaryData(),
            'project' => $this->getProjectData(),
            'client' => $this->getClientData(),
            'user' => $this->getUserData(),
            'detailed' => $this->getDetailedData(),
            default => [],
        };

        // Generate CSV content
        $csv = $this->generateCsvContent($data);

        // Send download response
        $filename = "time-report-{$this->reportType}-" . now()->format('Y-m-d') . '.csv';

        $this->dispatch('download-csv', [
            'content' => $csv,
            'filename' => $filename,
        ]);
    }

    protected function generateCsvContent($data): string
    {
        if ($this->reportType === 'summary') {
            return $this->generateSummaryCsv($data);
        } elseif ($this->reportType === 'detailed') {
            return $this->generateDetailedCsv($data);
        } else {
            return $this->generateGroupedCsv($data);
        }
    }

    protected function generateSummaryCsv(array $data): string
    {
        $output = "Time Report Summary\n";
        $output .= "Generated: " . now()->format('Y-m-d H:i:s') . "\n";
        $output .= "Period: {$this->dateFrom} to {$this->dateTo}\n\n";

        $output .= "Metric,Value\n";
        $output .= "Total Entries,{$data['total_entries']}\n";
        $output .= "Total Hours,{$data['total_hours']}\n";
        $output .= "Billable Hours,{$data['billable_hours']}\n";
        $output .= "Non-Billable Hours,{$data['non_billable_hours']}\n";
        $output .= "Total Amount,\${$data['total_amount']}\n";

        return $output;
    }

    protected function generateGroupedCsv(Collection $data): string
    {
        $headers = match ($this->reportType) {
            'project' => ['Project', 'Entries', 'Total Hours', 'Billable Hours', 'Total Amount'],
            'client' => ['Client', 'Entries', 'Total Hours', 'Billable Hours', 'Total Amount'],
            'user' => ['User', 'Entries', 'Total Hours', 'Billable Hours', 'Total Amount'],
            default => [],
        };

        $output = implode(',', $headers) . "\n";

        foreach ($data as $row) {
            $name = match ($this->reportType) {
                'project' => $row['project_name'],
                'client' => $row['client_name'],
                'user' => $row['user_name'],
                default => '',
            };

            $output .= "\"{$name}\",{$row['entries_count']},{$row['total_hours']},{$row['billable_hours']},\${$row['total_amount']}\n";
        }

        return $output;
    }

    protected function generateDetailedCsv(Collection $data): string
    {
        $output = "Date,User,Description,Project,Client,Duration,Billable,Rate,Amount,Status\n";

        foreach ($data as $entry) {
            $date = $entry->start_time->format('Y-m-d H:i');
            $user = $entry->user->name;
            $description = str_replace('"', '""', $entry->description);
            $project = $entry->project?->name ?? 'N/A';
            $client = $entry->client?->name ?? 'N/A';
            $duration = $entry->formatted_duration;
            $billable = $entry->billable ? 'Yes' : 'No';
            $rate = $entry->hourly_rate ? "\${$entry->hourly_rate}" : 'N/A';
            $amount = $entry->total_amount > 0 ? "\${$entry->total_amount}" : 'N/A';
            $status = $entry->status->label();

            $output .= "\"{$date}\",\"{$user}\",\"{$description}\",\"{$project}\",\"{$client}\",\"{$duration}\",{$billable},{$rate},{$amount},{$status}\n";
        }

        return $output;
    }

    public function render()
    {
        $reportData = match ($this->reportType) {
            'summary' => $this->getSummaryData(),
            'project' => $this->getProjectData(),
            'client' => $this->getClientData(),
            'user' => $this->getUserData(),
            'detailed' => $this->getDetailedData(),
            default => [],
        };

        $projects = Project::orderBy('name')->get();
        $clients = Client::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $statuses = TimeEntryStatus::cases();

        return view('livewire.tenant.time-entry.time-reports', [
            'reportData' => $reportData,
            'projects' => $projects,
            'clients' => $clients,
            'users' => $users,
            'statuses' => $statuses,
        ])->layout('layouts.tenant', [
            'title' => 'Time Reports',
            'header' => 'Time Reports',
        ]);
    }
}
