<div class="space-y-6">
    {{-- Report Type Tabs --}}
    <div class="bg-white rounded-lg shadow-sm">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button
                    wire:click="$set('reportType', 'summary')"
                    class="@if($reportType === 'summary') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                >
                    Summary
                </button>
                <button
                    wire:click="$set('reportType', 'category')"
                    class="@if($reportType === 'category') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                >
                    By Category
                </button>
                <button
                    wire:click="$set('reportType', 'project')"
                    class="@if($reportType === 'project') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                >
                    By Project
                </button>
                <button
                    wire:click="$set('reportType', 'client')"
                    class="@if($reportType === 'client') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                >
                    By Client
                </button>
                @if(auth()->user()->isOwner() || auth()->user()->isAdmin())
                    <button
                        wire:click="$set('reportType', 'user')"
                        class="@if($reportType === 'user') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                    >
                        By User
                    </button>
                @endif
                <button
                    wire:click="$set('reportType', 'detailed')"
                    class="@if($reportType === 'detailed') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                >
                    Detailed
                </button>
            </nav>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="space-y-4">
            {{-- Date Range --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                    <div class="col-span-2 md:col-span-1">
                        <input
                            type="date"
                            wire:model.live="dateFrom"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                        >
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <input
                            type="date"
                            wire:model.live="dateTo"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                        >
                    </div>
                    <div class="col-span-2 md:col-span-3 flex flex-wrap gap-2">
                        <button wire:click="setQuickRange('today')" class="px-3 py-1.5 text-xs font-medium rounded-md border border-gray-300 hover:bg-gray-50">Today</button>
                        <button wire:click="setQuickRange('yesterday')" class="px-3 py-1.5 text-xs font-medium rounded-md border border-gray-300 hover:bg-gray-50">Yesterday</button>
                        <button wire:click="setQuickRange('this_week')" class="px-3 py-1.5 text-xs font-medium rounded-md border border-gray-300 hover:bg-gray-50">This Week</button>
                        <button wire:click="setQuickRange('last_week')" class="px-3 py-1.5 text-xs font-medium rounded-md border border-gray-300 hover:bg-gray-50">Last Week</button>
                        <button wire:click="setQuickRange('this_month')" class="px-3 py-1.5 text-xs font-medium rounded-md border border-gray-300 hover:bg-gray-50">This Month</button>
                        <button wire:click="setQuickRange('last_month')" class="px-3 py-1.5 text-xs font-medium rounded-md border border-gray-300 hover:bg-gray-50">Last Month</button>
                        <button wire:click="setQuickRange('this_year')" class="px-3 py-1.5 text-xs font-medium rounded-md border border-gray-300 hover:bg-gray-50">This Year</button>
                    </div>
                </div>
            </div>

            {{-- Additional Filters --}}
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                <div>
                    <label for="category-filter" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select
                        id="category-filter"
                        wire:model.live="categoryFilter"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    >
                        <option value="all">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->value }}">{{ $category->icon() }} {{ $category->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select
                        id="status-filter"
                        wire:model.live="statusFilter"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    >
                        <option value="all">All Statuses</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="project-filter" class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                    <select
                        id="project-filter"
                        wire:model.live="projectFilter"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    >
                        <option value="all">All Projects</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="client-filter" class="block text-sm font-medium text-gray-700 mb-1">Client</label>
                    <select
                        id="client-filter"
                        wire:model.live="clientFilter"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    >
                        <option value="all">All Clients</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>

                @if(auth()->user()->isOwner() || auth()->user()->isAdmin())
                    <div>
                        <label for="user-filter" class="block text-sm font-medium text-gray-700 mb-1">User</label>
                        <select
                            id="user-filter"
                            wire:model.live="userFilter"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                        >
                            <option value="all">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label for="billable-filter" class="block text-sm font-medium text-gray-700 mb-1">Billable</label>
                    <select
                        id="billable-filter"
                        wire:model.live="billableFilter"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    >
                        <option value="all">All</option>
                        <option value="yes">Billable</option>
                        <option value="no">Non-Billable</option>
                    </select>
                </div>
            </div>

            {{-- Export Button --}}
            <div class="flex justify-end">
                <button
                    wire:click="exportCsv"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export CSV
                </button>
            </div>
        </div>
    </div>

    {{-- Report Content --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($reportType === 'summary')
            {{-- Summary Report --}}
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500">Total Expenses</h3>
                        <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $reportData['total_expenses'] }}</p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-blue-700">Total Amount</h3>
                        <p class="mt-2 text-3xl font-semibold text-blue-900">${{ number_format($reportData['total_amount'], 2) }}</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-green-700">Billable Amount</h3>
                        <p class="mt-2 text-3xl font-semibold text-green-900">${{ number_format($reportData['billable_amount'], 2) }}</p>
                    </div>
                    <div class="bg-orange-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-orange-700">Non-Billable</h3>
                        <p class="mt-2 text-3xl font-semibold text-orange-900">${{ number_format($reportData['non_billable_amount'], 2) }}</p>
                    </div>
                </div>

                {{-- By Category --}}
                @if(count($reportData['by_category']) > 0)
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Breakdown by Category</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expenses</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($reportData['by_category'] as $category => $data)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ \App\Enums\ExpenseCategory::from($category)->color() }}-100 text-{{ \App\Enums\ExpenseCategory::from($category)->color() }}-800">
                                                    {{ \App\Enums\ExpenseCategory::from($category)->icon() }} {{ \App\Enums\ExpenseCategory::from($category)->label() }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $data['count'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($data['amount'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- By Status --}}
                @if(count($reportData['by_status']) > 0)
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Breakdown by Status</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expenses</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($reportData['by_status'] as $status => $data)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ \App\Enums\ExpenseStatus::from($status)->color() }}-100 text-{{ \App\Enums\ExpenseStatus::from($status)->color() }}-800">
                                                    {{ \App\Enums\ExpenseStatus::from($status)->label() }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $data['count'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($data['amount'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

        @elseif(in_array($reportType, ['category', 'project', 'client', 'user']))
            {{-- Grouped Reports --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                @if($reportType === 'category') Category
                                @elseif($reportType === 'project') Project
                                @elseif($reportType === 'client') Client
                                @else User
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expenses</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Billable Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($reportData as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    @if($reportType === 'category')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ \App\Enums\ExpenseCategory::from($row['category'])->color() }}-100 text-{{ \App\Enums\ExpenseCategory::from($row['category'])->color() }}-800">
                                            {{ $row['category_icon'] }} {{ $row['category_name'] }}
                                        </span>
                                    @elseif($reportType === 'project')
                                        {{ $row['project_name'] }}
                                    @elseif($reportType === 'client')
                                        {{ $row['client_name'] }}
                                    @else
                                        {{ $row['user_name'] }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row['expenses_count'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($row['total_amount'], 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($row['billable_amount'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">
                                    No data available for the selected period and filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        @elseif($reportType === 'detailed')
            {{-- Detailed Report --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            @if(auth()->user()->isOwner() || auth()->user()->isAdmin())
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                            @endif
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Project</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($reportData as $expense)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $expense->expense_date->format('M d, Y') }}
                                </td>
                                @if(auth()->user()->isOwner() || auth()->user()->isAdmin())
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $expense->user->name }}</td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $expense->category->color() }}-100 text-{{ $expense->category->color() }}-800">
                                        {{ $expense->category->icon() }} {{ $expense->category->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="max-w-xs truncate" title="{{ $expense->description }}">
                                        {{ $expense->description }}
                                    </div>
                                    @if($expense->hasReceipt())
                                        <div class="text-xs text-gray-500 mt-1">
                                            <svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Receipt
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($expense->project)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $expense->project->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($expense->client)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            {{ $expense->client->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="font-medium text-gray-900">${{ number_format($expense->amount, 2) }}</div>
                                    @if($expense->billable)
                                        <div class="text-xs text-green-600">Billable</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $expense->status->color() }}-100 text-{{ $expense->status->color() }}-800">
                                        {{ $expense->status->label() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="@if(auth()->user()->isOwner() || auth()->user()->isAdmin()) 8 @else 7 @endif" class="px-6 py-12 text-center text-sm text-gray-500">
                                    No data available for the selected period and filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- CSV Download Script --}}
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('download-csv', (event) => {
                const blob = new Blob([event.content], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = event.filename;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            });
        });
    </script>
</div>
