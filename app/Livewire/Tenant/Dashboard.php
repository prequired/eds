<?php

declare(strict_types=1);

namespace App\Livewire\Tenant;

use App\Enums\ExpenseStatus;
use App\Enums\InvoiceStatus;
use App\Enums\TimeEntryStatus;
use App\Models\Tenant\Client;
use App\Models\Tenant\Expense;
use App\Models\Tenant\Invoice;
use App\Models\Tenant\Project;
use App\Models\Tenant\TimeEntry;
use App\Models\Tenant\User;
use Livewire\Component;

class Dashboard extends Component
{
    public function getQuickStatsProperty(): array
    {
        $currentMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        // Revenue stats
        $currentMonthRevenue = Invoice::whereBetween('invoice_date', [$currentMonth, now()])
            ->whereIn('status', [InvoiceStatus::PAID, InvoiceStatus::PARTIAL])
            ->sum('total_amount');

        $lastMonthRevenue = Invoice::whereBetween('invoice_date', [$lastMonth, $lastMonth->copy()->endOfMonth()])
            ->whereIn('status', [InvoiceStatus::PAID, InvoiceStatus::PARTIAL])
            ->sum('total_amount');

        $revenueChange = $lastMonthRevenue > 0
            ? (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100
            : 0;

        // Expenses stats
        $currentMonthExpenses = Expense::whereBetween('expense_date', [$currentMonth, now()])
            ->sum('amount');

        $lastMonthExpenses = Expense::whereBetween('expense_date', [$lastMonth, $lastMonth->copy()->endOfMonth()])
            ->sum('amount');

        $expenseChange = $lastMonthExpenses > 0
            ? (($currentMonthExpenses - $lastMonthExpenses) / $lastMonthExpenses) * 100
            : 0;

        // Time tracked
        $currentMonthHours = TimeEntry::whereBetween('start_time', [$currentMonth, now()])
            ->whereNotNull('end_time')
            ->sum('duration');
        $currentMonthHours = round($currentMonthHours / 3600, 1); // Convert to hours

        $lastMonthHours = TimeEntry::whereBetween('start_time', [$lastMonth, $lastMonth->copy()->endOfMonth()])
            ->whereNotNull('end_time')
            ->sum('duration');
        $lastMonthHours = round($lastMonthHours / 3600, 1);

        $hoursChange = $lastMonthHours > 0
            ? (($currentMonthHours - $lastMonthHours) / $lastMonthHours) * 100
            : 0;

        // Active projects
        $activeProjects = Project::has('timeEntries')->count();

        return [
            'revenue' => [
                'current' => $currentMonthRevenue,
                'change' => round($revenueChange, 1),
                'trend' => $revenueChange >= 0 ? 'up' : 'down',
            ],
            'expenses' => [
                'current' => $currentMonthExpenses,
                'change' => round($expenseChange, 1),
                'trend' => $expenseChange >= 0 ? 'up' : 'down',
            ],
            'hours' => [
                'current' => $currentMonthHours,
                'change' => round($hoursChange, 1),
                'trend' => $hoursChange >= 0 ? 'up' : 'down',
            ],
            'projects' => [
                'current' => $activeProjects,
            ],
        ];
    }

    public function getPendingApprovalsProperty(): array
    {
        if (!auth()->user()->isOwner() && !auth()->user()->isAdmin()) {
            return [
                'expenses' => 0,
                'time_entries' => 0,
            ];
        }

        return [
            'expenses' => Expense::submitted()->count(),
            'time_entries' => TimeEntry::where('status', TimeEntryStatus::SUBMITTED)->count(),
        ];
    }

    public function getRecentInvoicesProperty()
    {
        return Invoice::with('client')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function getRecentExpensesProperty()
    {
        $query = Expense::with(['user', 'category'])
            ->orderBy('expense_date', 'desc')
            ->limit(5);

        if (!auth()->user()->isOwner() && !auth()->user()->isAdmin()) {
            $query->forUser(auth()->id());
        }

        return $query->get();
    }

    public function getTopProjectsProperty()
    {
        return Project::withCount(['timeEntries'])
            ->with(['timeEntries' => function ($query) {
                $query->whereBetween('start_time', [now()->startOfMonth(), now()]);
            }])
            ->orderByDesc('time_entries_count')
            ->limit(5)
            ->get()
            ->map(function ($project) {
                $totalHours = $project->timeEntries->sum(function ($entry) {
                    return $entry->duration ?? 0;
                }) / 3600;

                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'hours' => round($totalHours, 1),
                    'entries' => $project->time_entries_count,
                ];
            });
    }

    public function getTopClientsProperty()
    {
        return Client::withSum(['invoices as revenue' => function ($query) {
            $query->whereBetween('invoice_date', [now()->startOfMonth(), now()])
                ->whereIn('status', [InvoiceStatus::PAID, InvoiceStatus::PARTIAL]);
        }], 'total_amount')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get()
            ->filter(fn ($client) => $client->revenue > 0)
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'revenue' => $client->revenue ?? 0,
                ];
            });
    }

    public function getTeamActivityProperty()
    {
        if (!auth()->user()->isOwner() && !auth()->user()->isAdmin()) {
            return collect();
        }

        return User::withCount([
            'timeEntries as hours' => function ($query) {
                $query->whereBetween('start_time', [now()->startOfMonth(), now()])
                    ->whereNotNull('end_time');
            }
        ])
            ->get()
            ->map(function ($user) {
                $totalSeconds = TimeEntry::where('user_id', $user->id)
                    ->whereBetween('start_time', [now()->startOfMonth(), now()])
                    ->whereNotNull('end_time')
                    ->sum('duration');

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'hours' => round($totalSeconds / 3600, 1),
                    'entries' => $user->hours,
                ];
            })
            ->sortByDesc('hours')
            ->take(5);
    }

    public function getRevenueChartDataProperty(): array
    {
        $data = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $revenue = Invoice::whereBetween('invoice_date', [$startOfMonth, $endOfMonth])
                ->whereIn('status', [InvoiceStatus::PAID, InvoiceStatus::PARTIAL])
                ->sum('total_amount');

            $labels[] = $date->format('M Y');
            $data[] = (float) $revenue;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    public function getExpenseChartDataProperty(): array
    {
        $data = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $expenses = Expense::whereBetween('expense_date', [$startOfMonth, $endOfMonth])
                ->sum('amount');

            $labels[] = $date->format('M Y');
            $data[] = (float) $expenses;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    public function render()
    {
        return view('livewire.tenant.dashboard', [
            'quickStats' => $this->quickStats,
            'pendingApprovals' => $this->pendingApprovals,
            'recentInvoices' => $this->recentInvoices,
            'recentExpenses' => $this->recentExpenses,
            'topProjects' => $this->topProjects,
            'topClients' => $this->topClients,
            'teamActivity' => $this->teamActivity,
            'revenueChartData' => $this->revenueChartData,
            'expenseChartData' => $this->expenseChartData,
        ])->layout('layouts.tenant', [
            'title' => 'Dashboard',
            'header' => 'Dashboard',
        ]);
    }
}
