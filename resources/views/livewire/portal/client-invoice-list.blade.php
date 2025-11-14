<div>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Invoices</h1>
        <p class="mt-2 text-gray-600">View and manage your invoices</p>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Total Invoices</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $this->totalStats['total_invoices'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Unpaid Invoices</p>
            <p class="mt-2 text-2xl font-bold text-orange-600">{{ $this->totalStats['unpaid_invoices'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Total Outstanding</p>
            <p class="mt-2 text-2xl font-bold text-orange-600">${{ number_format($this->totalStats['total_outstanding'], 2) }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <div class="flex flex-wrap gap-4 items-center">
            <div>
                <label class="text-sm font-medium text-gray-700 mr-2">Status:</label>
                <select wire:model.live="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">All Invoices</option>
                    <option value="unpaid">Unpaid</option>
                    <option value="paid">Paid</option>
                    <option value="overdue">Overdue</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($this->invoices->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <button wire:click="sortBy('invoice_number')" class="flex items-center text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700">
                                    Invoice #
                                    @if($sortBy === 'invoice_number')
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M{{ $sortDirection === 'asc' ? '5 15l7-7 7 7' : '19 9l-7 7-7-7' }}" />
                                        </svg>
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left">
                                <button wire:click="sortBy('invoice_date')" class="flex items-center text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700">
                                    Date
                                    @if($sortBy === 'invoice_date')
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M{{ $sortDirection === 'asc' ? '5 15l7-7 7 7' : '19 9l-7 7-7-7' }}" />
                                        </svg>
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left">
                                <button wire:click="sortBy('due_date')" class="flex items-center text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700">
                                    Due Date
                                    @if($sortBy === 'due_date')
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M{{ $sortDirection === 'asc' ? '5 15l7-7 7 7' : '19 9l-7 7-7-7' }}" />
                                        </svg>
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left">
                                <button wire:click="sortBy('total_amount')" class="flex items-center text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700">
                                    Amount
                                    @if($sortBy === 'total_amount')
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M{{ $sortDirection === 'asc' ? '5 15l7-7 7 7' : '19 9l-7 7-7-7' }}" />
                                        </svg>
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($this->invoices as $invoice)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $invoice->invoice_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $invoice->due_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    ${{ number_format($invoice->total_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold {{ $invoice->balance_due > 0 ? 'text-orange-600' : 'text-green-600' }}">
                                    ${{ number_format($invoice->balance_due, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $invoice->status->color() }}">
                                        {{ $invoice->status->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('portal.invoices.show', $invoice) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $this->invoices->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No invoices found</h3>
                <p class="text-sm text-gray-500">
                    @if($status !== 'all')
                        No {{ $status }} invoices at this time.
                    @else
                        You don't have any invoices yet.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>
