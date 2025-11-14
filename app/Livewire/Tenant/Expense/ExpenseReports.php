<?php

declare(strict_types=1);

namespace App\Livewire\Tenant\Expense;

use App\Enums\ExpenseCategory;
use App\Enums\ExpenseStatus;
use App\Models\Tenant\Client;
use App\Models\Tenant\Expense;
use App\Models\Tenant\Project;
use App\Models\Tenant\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Component;

class ExpenseReports extends Component
{
    #[Url(as: 'report')]
    public string $reportType = 'summary';

    #[Url(as: 'from')]
    public string $dateFrom = '';

    #[Url(as: 'to')]
    public string $dateTo = '';

    #[Url(as: 'category')]
    public string $categoryFilter = 'all';

    #[Url(as: 'status')]
    public string $statusFilter = 'all';

    #[Url(as: 'project')]
    public string $projectFilter = 'all';

    #[Url(as: 'client')]
    public string $clientFilter = 'all';

    #[Url(as: 'user')]
    public string $userFilter = 'all';

    #[Url(as: 'billable')]
    public string $billableFilter = 'all';

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
        $query = Expense::with(['user', 'project', 'client'])
            ->when($this->dateFrom, function ($q) {
                $q->whereDate('expense_date', '>=', Carbon::parse($this->dateFrom));
            })
            ->when($this->dateTo, function ($q) {
                $q->whereDate('expense_date', '<=', Carbon::parse($this->dateTo));
            })
            ->when($this->categoryFilter !== 'all', function ($q) {
                $q->where('category', $this->categoryFilter);
            })
            ->when($this->statusFilter !== 'all', function ($q) {
                $q->where('status', $this->statusFilter);
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
            });

        // Non-admin users only see their own expenses
        if (!auth()->user()->isOwner() && !auth()->user()->isAdmin()) {
            $query->forUser(auth()->id());
        }

        return $query;
    }

    public function getSummaryData(): array
    {
        $expenses = $this->getBaseQuery()->get();

        $totalAmount = $expenses->sum('amount');
        $billableAmount = $expenses->where('billable', true)->sum('amount');
        $nonBillableAmount = $expenses->where('billable', false)->sum('amount');

        $byStatus = $expenses->groupBy('status')->map(function ($group) {
            return [
                'count' => $group->count(),
                'amount' => $group->sum('amount'),
            ];
        });

        $byCategory = $expenses->groupBy('category')->map(function ($group) {
            return [
                'count' => $group->count(),
                'amount' => $group->sum('amount'),
            ];
        });

        return [
            'total_expenses' => $expenses->count(),
            'total_amount' => round($totalAmount, 2),
            'billable_amount' => round($billableAmount, 2),
            'non_billable_amount' => round($nonBillableAmount, 2),
            'by_status' => $byStatus,
            'by_category' => $byCategory,
        ];
    }

    public function getCategoryData(): Collection
    {
        $expenses = $this->getBaseQuery()->get();

        return $expenses->groupBy('category')->map(function ($group) {
            $category = $group->first()->category;
            return [
                'category' => $category->value,
                'category_name' => $category->label(),
                'category_icon' => $category->icon(),
                'expenses_count' => $group->count(),
                'total_amount' => round($group->sum('amount'), 2),
                'billable_amount' => round($group->where('billable', true)->sum('amount'), 2),
            ];
        })->sortByDesc('total_amount')->values();
    }

    public function getProjectData(): Collection
    {
        $expenses = $this->getBaseQuery()->get();

        return $expenses->groupBy('project_id')->map(function ($group) {
            $project = $group->first()->project;
            return [
                'project_id' => $project?->id,
                'project_name' => $project?->name ?? 'No Project',
                'expenses_count' => $group->count(),
                'total_amount' => round($group->sum('amount'), 2),
                'billable_amount' => round($group->where('billable', true)->sum('amount'), 2),
            ];
        })->sortByDesc('total_amount')->values();
    }

    public function getClientData(): Collection
    {
        $expenses = $this->getBaseQuery()->get();

        return $expenses->groupBy('client_id')->map(function ($group) {
            $client = $group->first()->client;
            return [
                'client_id' => $client?->id,
                'client_name' => $client?->name ?? 'No Client',
                'expenses_count' => $group->count(),
                'total_amount' => round($group->sum('amount'), 2),
                'billable_amount' => round($group->where('billable', true)->sum('amount'), 2),
            ];
        })->sortByDesc('total_amount')->values();
    }

    public function getUserData(): Collection
    {
        $expenses = $this->getBaseQuery()->get();

        return $expenses->groupBy('user_id')->map(function ($group) {
            $user = $group->first()->user;
            return [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'expenses_count' => $group->count(),
                'total_amount' => round($group->sum('amount'), 2),
                'billable_amount' => round($group->where('billable', true)->sum('amount'), 2),
            ];
        })->sortByDesc('total_amount')->values();
    }

    public function getDetailedData(): Collection
    {
        return $this->getBaseQuery()
            ->orderBy('expense_date', 'desc')
            ->get();
    }

    public function exportCsv(): void
    {
        $data = match ($this->reportType) {
            'summary' => $this->getSummaryData(),
            'category' => $this->getCategoryData(),
            'project' => $this->getProjectData(),
            'client' => $this->getClientData(),
            'user' => $this->getUserData(),
            'detailed' => $this->getDetailedData(),
            default => [],
        };

        $csv = $this->generateCsvContent($data);
        $filename = "expense-report-{$this->reportType}-" . now()->format('Y-m-d') . '.csv';

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
        $output = "Expense Report Summary\n";
        $output .= "Generated: " . now()->format('Y-m-d H:i:s') . "\n";
        $output .= "Period: {$this->dateFrom} to {$this->dateTo}\n\n";

        $output .= "Metric,Value\n";
        $output .= "Total Expenses,{$data['total_expenses']}\n";
        $output .= "Total Amount,\${$data['total_amount']}\n";
        $output .= "Billable Amount,\${$data['billable_amount']}\n";
        $output .= "Non-Billable Amount,\${$data['non_billable_amount']}\n";

        return $output;
    }

    protected function generateGroupedCsv(Collection $data): string
    {
        $headers = match ($this->reportType) {
            'category' => ['Category', 'Expenses', 'Total Amount', 'Billable Amount'],
            'project' => ['Project', 'Expenses', 'Total Amount', 'Billable Amount'],
            'client' => ['Client', 'Expenses', 'Total Amount', 'Billable Amount'],
            'user' => ['User', 'Expenses', 'Total Amount', 'Billable Amount'],
            default => [],
        };

        $output = implode(',', $headers) . "\n";

        foreach ($data as $row) {
            $name = match ($this->reportType) {
                'category' => $row['category_icon'] . ' ' . $row['category_name'],
                'project' => $row['project_name'],
                'client' => $row['client_name'],
                'user' => $row['user_name'],
                default => '',
            };

            $output .= "\"{$name}\",{$row['expenses_count']},\${$row['total_amount']},\${$row['billable_amount']}\n";
        }

        return $output;
    }

    protected function generateDetailedCsv(Collection $data): string
    {
        $output = "Date,User,Category,Description,Project,Client,Amount,Billable,Status,Receipt\n";

        foreach ($data as $expense) {
            $date = $expense->expense_date->format('Y-m-d');
            $user = $expense->user->name;
            $category = $expense->category->label();
            $description = str_replace('"', '""', $expense->description);
            $project = $expense->project?->name ?? 'N/A';
            $client = $expense->client?->name ?? 'N/A';
            $amount = "\${$expense->amount}";
            $billable = $expense->billable ? 'Yes' : 'No';
            $status = $expense->status->label();
            $receipt = $expense->hasReceipt() ? 'Yes' : 'No';

            $output .= "\"{$date}\",\"{$user}\",\"{$category}\",\"{$description}\",\"{$project}\",\"{$client}\",{$amount},{$billable},{$status},{$receipt}\n";
        }

        return $output;
    }

    public function render()
    {
        $reportData = match ($this->reportType) {
            'summary' => $this->getSummaryData(),
            'category' => $this->getCategoryData(),
            'project' => $this->getProjectData(),
            'client' => $this->getClientData(),
            'user' => $this->getUserData(),
            'detailed' => $this->getDetailedData(),
            default => [],
        };

        $categories = ExpenseCategory::cases();
        $statuses = ExpenseStatus::cases();
        $projects = Project::orderBy('name')->get();
        $clients = Client::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('livewire.tenant.expense.expense-reports', [
            'reportData' => $reportData,
            'categories' => $categories,
            'statuses' => $statuses,
            'projects' => $projects,
            'clients' => $clients,
            'users' => $users,
        ])->layout('layouts.tenant', [
            'title' => 'Expense Reports',
            'header' => 'Expense Reports',
        ]);
    }
}
