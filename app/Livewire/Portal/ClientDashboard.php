<?php

declare(strict_types=1);

namespace App\Livewire\Portal;

use App\Enums\InvoiceStatus;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ClientDashboard extends Component
{
    public function getStatsProperty(): array
    {
        $client = auth('client')->user();

        $totalInvoiced = $client->invoices()
            ->whereIn('status', [
                InvoiceStatus::SENT,
                InvoiceStatus::PARTIAL,
                InvoiceStatus::PAID,
                InvoiceStatus::OVERDUE,
            ])
            ->sum('total_amount');

        $totalPaid = $client->invoices()
            ->where('status', InvoiceStatus::PAID)
            ->sum('total_amount');

        $totalOutstanding = $client->invoices()
            ->whereIn('status', [
                InvoiceStatus::SENT,
                InvoiceStatus::PARTIAL,
                InvoiceStatus::OVERDUE,
            ])
            ->sum('balance_due');

        $overdueCount = $client->invoices()
            ->where('status', InvoiceStatus::OVERDUE)
            ->count();

        return [
            'total_invoiced' => $totalInvoiced,
            'total_paid' => $totalPaid,
            'total_outstanding' => $totalOutstanding,
            'overdue_count' => $overdueCount,
        ];
    }

    public function getRecentInvoicesProperty()
    {
        return auth('client')->user()
            ->invoices()
            ->latest()
            ->limit(5)
            ->get();
    }

    public function getActiveProjectsProperty()
    {
        return auth('client')->user()
            ->projects()
            ->where('status', \App\Enums\ProjectStatus::ACTIVE)
            ->get();
    }

    public function logout(): void
    {
        auth('client')->logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->redirect(route('portal.login'), navigate: true);
    }

    #[Layout('layouts.portal')]
    public function render(): View
    {
        return view('livewire.portal.client-dashboard');
    }
}
