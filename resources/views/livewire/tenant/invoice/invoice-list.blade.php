<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Invoices</h1>
            <p class="mt-1 text-sm text-gray-500">Create and manage client invoices</p>
        </div>
        <div class="flex items-center space-x-3">
            <a
                href="{{ route('invoices.create') }}"
                wire:navigate
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Invoice
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Total</div>
            <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Draft</div>
            <div class="mt-1 text-2xl font-semibold text-gray-600">{{ $stats['draft'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Sent</div>
            <div class="mt-1 text-2xl font-semibold text-blue-600">{{ $stats['sent'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Overdue</div>
            <div class="mt-1 text-2xl font-semibold text-red-600">{{ $stats['overdue'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Unpaid</div>
            <div class="mt-1 text-2xl font-semibold text-orange-600">{{ $stats['unpaid'] }}</div>
        </div>
    </div>

    <!-- Search and Filter Bar -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Search -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search by invoice number or client..."
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>

            <!-- Status Filter -->
            <div>
                <select wire:model.live="statusFilter" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">All Statuses</option>
                    <option value="overdue">Overdue</option>
                    <option value="unpaid">Unpaid</option>
                    @foreach($statuses as $status)
                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Invoices Table -->
    @if($invoices->count() > 0)
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Invoice
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Client
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Issue Date
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Due Date
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Amount
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($invoices as $invoice)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $invoice->client->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $invoice->issue_date->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $invoice->due_date->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${{ number_format($invoice->total, 2) }}</div>
                        @if($invoice->amount_paid > 0)
                        <div class="text-xs text-gray-500">Paid: ${{ number_format($invoice->amount_paid, 2) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($invoice->status->value === 'paid') bg-green-100 text-green-800
                            @elseif($invoice->status->value === 'overdue') bg-red-100 text-red-800
                            @elseif($invoice->status->value === 'sent') bg-blue-100 text-blue-800
                            @elseif($invoice->status->value === 'draft') bg-gray-100 text-gray-800
                            @elseif($invoice->status->value === 'partially_paid') bg-yellow-100 text-yellow-800
                            @elseif($invoice->status->value === 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $invoice->status->label() }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('invoices.view', $invoice) }}" wire:navigate class="text-blue-600 hover:text-blue-900">
                                View
                            </a>
                            @if($invoice->canEdit())
                            <a href="{{ route('invoices.edit', $invoice) }}" wire:navigate class="text-blue-600 hover:text-blue-900">
                                Edit
                            </a>
                            @endif
                            @can('invoices.delete')
                            <button
                                wire:click="deleteInvoice('{{ $invoice->id }}')"
                                wire:confirm="Are you sure you want to delete this invoice?"
                                class="text-red-600 hover:text-red-900">
                                Delete
                            </button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $invoices->links() }}
    </div>
    @else
    <!-- Empty State -->
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No invoices found</h3>
        <p class="mt-1 text-sm text-gray-500">
            @if($search)
                No invoices match your search "{{ $search }}".
            @else
                Get started by creating your first invoice.
            @endif
        </p>
        @if(!$search)
        <div class="mt-6">
            <a
                href="{{ route('invoices.create') }}"
                wire:navigate
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Your First Invoice
            </a>
        </div>
        @endif
    </div>
    @endif
</div>
