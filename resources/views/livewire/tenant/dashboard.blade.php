<div class="space-y-6">
    {{-- Quick Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Revenue Card --}}
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Revenue (This Month)</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">${{ number_format($quickStats['revenue']['current'], 0) }}</p>
                    <div class="mt-2 flex items-center text-sm">
                        @if($quickStats['revenue']['trend'] === 'up')
                            <svg class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                            <span class="text-green-600 font-medium">{{ abs($quickStats['revenue']['change']) }}%</span>
                        @else
                            <svg class="w-4 h-4 text-red-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                            <span class="text-red-600 font-medium">{{ abs($quickStats['revenue']['change']) }}%</span>
                        @endif
                        <span class="text-gray-500 ml-1">vs last month</span>
                    </div>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Expenses Card --}}
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Expenses (This Month)</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">${{ number_format($quickStats['expenses']['current'], 0) }}</p>
                    <div class="mt-2 flex items-center text-sm">
                        @if($quickStats['expenses']['trend'] === 'up')
                            <svg class="w-4 h-4 text-red-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                            <span class="text-red-600 font-medium">{{ abs($quickStats['expenses']['change']) }}%</span>
                        @else
                            <svg class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                            <span class="text-green-600 font-medium">{{ abs($quickStats['expenses']['change']) }}%</span>
                        @endif
                        <span class="text-gray-500 ml-1">vs last month</span>
                    </div>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Hours Tracked Card --}}
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Hours Tracked (This Month)</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ number_format($quickStats['hours']['current'], 1) }}</p>
                    <div class="mt-2 flex items-center text-sm">
                        @if($quickStats['hours']['trend'] === 'up')
                            <svg class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                            <span class="text-green-600 font-medium">{{ abs($quickStats['hours']['change']) }}%</span>
                        @else
                            <svg class="w-4 h-4 text-red-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                            <span class="text-red-600 font-medium">{{ abs($quickStats['hours']['change']) }}%</span>
                        @endif
                        <span class="text-gray-500 ml-1">vs last month</span>
                    </div>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Active Projects Card --}}
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Active Projects</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $quickStats['projects']['current'] }}</p>
                    <p class="mt-2 text-sm text-gray-500">with time entries</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Pending Approvals (Admin Only) --}}
    @if((auth()->user()->isOwner() || auth()->user()->isAdmin()) && ($pendingApprovals['expenses'] > 0 || $pendingApprovals['time_entries'] > 0))
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-medium text-yellow-800">Pending Approvals</p>
                    <p class="text-sm text-yellow-700">
                        @if($pendingApprovals['expenses'] > 0)
                            {{ $pendingApprovals['expenses'] }} expense{{ $pendingApprovals['expenses'] > 1 ? 's' : '' }}
                        @endif
                        @if($pendingApprovals['expenses'] > 0 && $pendingApprovals['time_entries'] > 0)
                            and
                        @endif
                        @if($pendingApprovals['time_entries'] > 0)
                            {{ $pendingApprovals['time_entries'] }} time {{ $pendingApprovals['time_entries'] > 1 ? 'entries' : 'entry' }}
                        @endif
                        waiting for approval
                    </p>
                </div>
                <div class="flex space-x-2">
                    @if($pendingApprovals['expenses'] > 0)
                        <a href="{{ route('expenses.index') }}?status=submitted" wire:navigate class="px-3 py-1.5 text-xs font-medium rounded-md bg-yellow-100 text-yellow-800 hover:bg-yellow-200">
                            Review Expenses
                        </a>
                    @endif
                    @if($pendingApprovals['time_entries'] > 0)
                        <a href="{{ route('time-tracking.index') }}?status=submitted" wire:navigate class="px-3 py-1.5 text-xs font-medium rounded-md bg-yellow-100 text-yellow-800 hover:bg-yellow-200">
                            Review Time
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Revenue Chart --}}
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Revenue Trend (Last 7 Months)</h3>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Expense Chart --}}
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Expense Trend (Last 7 Months)</h3>
            <div class="h-64">
                <canvas id="expenseChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Activity and Insights Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Invoices --}}
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Recent Invoices</h3>
                    <a href="{{ route('invoices.index') }}" wire:navigate class="text-sm text-blue-600 hover:text-blue-700">View all</a>
                </div>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($recentInvoices as $invoice)
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</p>
                                <p class="text-sm text-gray-500">{{ $invoice->client->name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">${{ number_format($invoice->total_amount, 2) }}</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $invoice->status->color() }}-100 text-{{ $invoice->status->color() }}-800">
                                    {{ $invoice->status->label() }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-gray-500">
                        No recent invoices
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Expenses --}}
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Recent Expenses</h3>
                    <a href="{{ route('expenses.index') }}" wire:navigate class="text-sm text-blue-600 hover:text-blue-700">View all</a>
                </div>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($recentExpenses as $expense)
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $expense->description }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ $expense->category->icon() }} {{ $expense->category->label() }}
                                    @if(auth()->user()->isOwner() || auth()->user()->isAdmin())
                                        • {{ $expense->user->name }}
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">${{ number_format($expense->amount, 2) }}</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $expense->status->color() }}-100 text-{{ $expense->status->color() }}-800">
                                    {{ $expense->status->label() }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-gray-500">
                        No recent expenses
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Top Projects and Clients --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Top Projects --}}
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Top Projects (This Month)</h3>
            </div>
            <div class="p-6">
                @forelse($topProjects as $project)
                    <div class="flex items-center justify-between mb-4 last:mb-0">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $project['name'] }}</p>
                            <p class="text-xs text-gray-500">{{ $project['entries'] }} {{ $project['entries'] === 1 ? 'entry' : 'entries' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">{{ $project['hours'] }}h</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">No project data available</p>
                @endforelse
            </div>
        </div>

        {{-- Top Clients --}}
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Top Clients (This Month)</h3>
            </div>
            <div class="p-6">
                @forelse($topClients as $client)
                    <div class="flex items-center justify-between mb-4 last:mb-0">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $client['name'] }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">${{ number_format($client['revenue'], 2) }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">No client revenue data available</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Team Activity (Admin Only) --}}
    @if(auth()->user()->isOwner() || auth()->user()->isAdmin())
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Team Activity (This Month)</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($teamActivity as $member)
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $member['name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $member['entries'] }} {{ $member['entries'] === 1 ? 'entry' : 'entries' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ $member['hours'] }}h</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">No team activity data available</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    {{-- Charts Script --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Revenue Chart
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: @js($revenueChartData['labels']),
                    datasets: [{
                        label: 'Revenue',
                        data: @js($revenueChartData['data']),
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            // Expense Chart
            const expenseCtx = document.getElementById('expenseChart').getContext('2d');
            new Chart(expenseCtx, {
                type: 'line',
                data: {
                    labels: @js($expenseChartData['labels']),
                    datasets: [{
                        label: 'Expenses',
                        data: @js($expenseChartData['data']),
                        borderColor: 'rgb(239, 68, 68)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</div>
