<div>
    <!-- Welcome Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Welcome, {{ auth('client')->user()->name }}!</h1>
        <p class="mt-2 text-gray-600">Here's an overview of your account</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Invoiced -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Invoiced</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        ${{ number_format($this->stats['total_invoiced'], 2) }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Paid -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Paid</p>
                    <p class="mt-2 text-3xl font-bold text-green-600">
                        ${{ number_format($this->stats['total_paid'], 2) }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Outstanding -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Outstanding</p>
                    <p class="mt-2 text-3xl font-bold text-orange-600">
                        ${{ number_format($this->stats['total_outstanding'], 2) }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Overdue -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Overdue Invoices</p>
                    <p class="mt-2 text-3xl font-bold {{ $this->stats['overdue_count'] > 0 ? 'text-red-600' : 'text-gray-900' }}">
                        {{ $this->stats['overdue_count'] }}
                    </p>
                </div>
                <div class="w-12 h-12 {{ $this->stats['overdue_count'] > 0 ? 'bg-red-100' : 'bg-gray-100' }} rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 {{ $this->stats['overdue_count'] > 0 ? 'text-red-600' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Invoices -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Recent Invoices</h2>
            <a href="{{ route('portal.invoices') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                View All →
            </a>
        </div>

        @if($this->recentInvoices->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($this->recentInvoices as $invoice)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $invoice->invoice_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $invoice->invoice_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    ${{ number_format($invoice->total_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $invoice->status->color() }}">
                                        {{ $invoice->status->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('portal.invoices.show', $invoice) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-sm text-gray-500">No invoices yet</p>
            </div>
        @endif
    </div>

    <!-- Active Projects (if any) -->
    @if($this->activeProjects->count() > 0)
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Active Projects</h2>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($this->activeProjects as $project)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 mb-2">{{ $project->name }}</h3>
                            @if($project->description)
                                <p class="text-sm text-gray-600 mb-3">{{ Str::limit($project->description, 100) }}</p>
                            @endif
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span class="px-2 py-1 bg-gray-100 rounded">{{ $project->status->label() }}</span>
                                @if($project->due_at)
                                    <span>Due: {{ $project->due_at->format('M d, Y') }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
