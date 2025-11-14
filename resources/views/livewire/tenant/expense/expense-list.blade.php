<div class="space-y-6">
    {{-- Header with Stats --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500">Total Expenses</h3>
                <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $expenses->total() }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Total Amount</h3>
                <p class="mt-2 text-3xl font-semibold text-gray-900">${{ number_format($totalAmount, 2) }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Billable Amount</h3>
                <p class="mt-2 text-3xl font-semibold text-green-600">${{ number_format($billableAmount, 2) }}</p>
            </div>
        </div>

        {{-- Add Expense Button --}}
        <div class="mt-6">
            <a
                href="{{ route('expenses.create') }}"
                wire:navigate
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Expense
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            {{-- Search --}}
            <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input
                    type="text"
                    id="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by description..."
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                >
            </div>

            {{-- Category Filter --}}
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

            {{-- Status Filter --}}
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

            {{-- Billable Filter --}}
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

        <div class="mt-4 grid grid-cols-1 md:grid-cols-5 gap-4">
            {{-- Project Filter --}}
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

            {{-- Client Filter --}}
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

            {{-- Clear Filters --}}
            <div class="flex items-end">
                <button
                    type="button"
                    wire:click="$set('search', ''); $set('categoryFilter', 'all'); $set('statusFilter', 'all'); $set('projectFilter', 'all'); $set('clientFilter', 'all'); $set('billableFilter', 'all')"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Clear Filters
                </button>
            </div>
        </div>
    </div>

    {{-- Expenses Table --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($expenses->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            @if(auth()->user()->isOwner() || auth()->user()->isAdmin())
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            @endif
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($expenses as $expense)
                            <tr class="hover:bg-gray-50">
                                @if(auth()->user()->isOwner() || auth()->user()->isAdmin())
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $expense->user->name }}
                                    </td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $expense->expense_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $expense->category->color() }}-100 text-{{ $expense->category->color() }}-800">
                                        {{ $expense->category->icon() }} {{ $expense->category->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="max-w-xs">
                                        <div class="font-medium truncate" title="{{ $expense->description }}">{{ $expense->description }}</div>
                                        @if($expense->hasReceipt())
                                            <div class="text-xs text-gray-500 mt-1">
                                                <svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                Receipt attached
                                            </div>
                                        @endif
                                    </div>
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
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        {{-- Submit --}}
                                        @if($expense->status->canSubmit() && $expense->user_id === auth()->id())
                                            <button
                                                wire:click="submitExpense('{{ $expense->id }}')"
                                                wire:confirm="Submit this expense for approval?"
                                                class="text-blue-600 hover:text-blue-900"
                                                title="Submit for Approval"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>
                                        @endif

                                        {{-- Approve --}}
                                        @if($expense->status->canApprove() && (auth()->user()->isOwner() || auth()->user()->isAdmin()))
                                            <button
                                                wire:click="approveExpense('{{ $expense->id }}')"
                                                wire:confirm="Approve this expense?"
                                                class="text-green-600 hover:text-green-900"
                                                title="Approve"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                        @endif

                                        {{-- Reject --}}
                                        @if($expense->status->canReject() && (auth()->user()->isOwner() || auth()->user()->isAdmin()))
                                            <button
                                                wire:click="rejectExpense('{{ $expense->id }}')"
                                                wire:confirm="Reject this expense?"
                                                class="text-red-600 hover:text-red-900"
                                                title="Reject"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        @endif

                                        {{-- Reimburse --}}
                                        @if($expense->status->canReimburse() && (auth()->user()->isOwner() || auth()->user()->isAdmin()))
                                            <button
                                                wire:click="reimburseExpense('{{ $expense->id }}')"
                                                wire:confirm="Mark this expense as reimbursed?"
                                                class="text-purple-600 hover:text-purple-900"
                                                title="Mark as Reimbursed"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            </button>
                                        @endif

                                        {{-- Edit --}}
                                        @if($expense->canBeEdited() && ($expense->user_id === auth()->id() || auth()->user()->isOwner() || auth()->user()->isAdmin()))
                                            <a
                                                href="{{ route('expenses.edit', $expense) }}"
                                                wire:navigate
                                                class="text-blue-600 hover:text-blue-900"
                                                title="Edit"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                        @endif

                                        {{-- Delete --}}
                                        @if($expense->canBeDeleted() && ($expense->user_id === auth()->id() || auth()->user()->isOwner() || auth()->user()->isAdmin()))
                                            <button
                                                wire:click="deleteExpense('{{ $expense->id }}')"
                                                wire:confirm="Are you sure you want to delete this expense?"
                                                class="text-red-600 hover:text-red-900"
                                                title="Delete"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $expenses->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No expenses found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if($search || $categoryFilter !== 'all' || $statusFilter !== 'all' || $projectFilter !== 'all' || $clientFilter !== 'all' || $billableFilter !== 'all')
                        Try adjusting your filters or clearing them.
                    @else
                        Get started by creating a new expense.
                    @endif
                </p>
                @if(!$search && $categoryFilter === 'all' && $statusFilter === 'all')
                    <div class="mt-6">
                        <a
                            href="{{ route('expenses.create') }}"
                            wire:navigate
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            New Expense
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
