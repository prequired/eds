<div class="py-6">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Actions Bar --}}
        <div class="mb-6 flex items-center justify-between">
            <a
                href="{{ route('invoices.index') }}"
                wire:navigate
                class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Invoices
            </a>

            <div class="flex items-center space-x-3">
                @if($invoice->canSend())
                <button
                    wire:click="sendInvoice"
                    wire:confirm="Are you sure you want to send this invoice to the client?"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Send Invoice
                </button>
                @endif

                @if(!$invoice->isFullyPaid() && $invoice->status !== \App\Enums\InvoiceStatus::CANCELLED)
                <button
                    wire:click="openPaymentModal"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Record Payment
                </button>
                @endif

                @if($invoice->canEdit())
                <a
                    href="{{ route('invoices.edit', $invoice) }}"
                    wire:navigate
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
                @endif

                @if($invoice->status === \App\Enums\InvoiceStatus::DRAFT || ($invoice->status === \App\Enums\InvoiceStatus::SENT && !$invoice->isPartiallyPaid()))
                <button
                    wire:click="cancelInvoice"
                    wire:confirm="Are you sure you want to cancel this invoice?"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50"
                >
                    Cancel Invoice
                </button>
                @endif
            </div>
        </div>

        {{-- Invoice Card --}}
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            {{-- Header --}}
            <div class="px-6 py-5 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $invoice->invoice_number }}</h1>
                        <p class="mt-1 text-sm text-gray-500">{{ $invoice->client->name }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
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
                    </div>
                </div>
            </div>

            {{-- Details --}}
            <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Bill To</h3>
                    <div class="text-sm text-gray-900">
                        <p class="font-medium">{{ $invoice->client->name }}</p>
                        @if($invoice->client->company)
                        <p>{{ $invoice->client->company }}</p>
                        @endif
                        @if($invoice->client->email)
                        <p>{{ $invoice->client->email }}</p>
                        @endif
                        @if($invoice->client->phone)
                        <p>{{ $invoice->client->phone }}</p>
                        @endif
                    </div>
                </div>

                <div class="text-right">
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Issue Date:</span>
                            <span class="text-gray-900">{{ $invoice->issue_date->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Due Date:</span>
                            <span class="text-gray-900 @if($invoice->isOverdue()) text-red-600 font-medium @endif">
                                {{ $invoice->due_date->format('M d, Y') }}
                                @if($invoice->isOverdue())
                                <span class="text-xs">(Overdue)</span>
                                @endif
                            </span>
                        </div>
                        @if($invoice->paid_date)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Paid Date:</span>
                            <span class="text-gray-900">{{ $invoice->paid_date->format('M d, Y') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Line Items --}}
            <div class="px-6 py-5 border-t border-gray-200">
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Items</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="pb-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="pb-2 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                                <th class="pb-2 text-right text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                                <th class="pb-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($invoice->items as $item)
                            <tr>
                                <td class="py-3 text-sm text-gray-900">{{ $item->description }}</td>
                                <td class="py-3 text-sm text-gray-900 text-right">{{ number_format($item->quantity, 2) }}</td>
                                <td class="py-3 text-sm text-gray-900 text-right">${{ number_format($item->unit_price, 2) }}</td>
                                <td class="py-3 text-sm text-gray-900 text-right">${{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Totals --}}
            <div class="px-6 py-5 border-t border-gray-200 bg-gray-50">
                <div class="flex justify-end">
                    <div class="w-64 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="text-gray-900 font-medium">${{ number_format($invoice->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tax ({{ number_format($invoice->tax_rate, 2) }}%):</span>
                            <span class="text-gray-900 font-medium">${{ number_format($invoice->tax_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t border-gray-300 pt-2">
                            <span class="text-gray-900">Total:</span>
                            <span class="text-gray-900">${{ number_format($invoice->total, 2) }}</span>
                        </div>
                        @if($invoice->amount_paid > 0)
                        <div class="flex justify-between text-sm text-green-600">
                            <span>Amount Paid:</span>
                            <span class="font-medium">${{ number_format($invoice->amount_paid, 2) }}</span>
                        </div>
                        @if(!$invoice->isFullyPaid())
                        <div class="flex justify-between text-sm text-red-600">
                            <span>Balance Due:</span>
                            <span class="font-medium">${{ number_format($invoice->remaining_amount, 2) }}</span>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
            </div>

            {{-- Notes, Terms, Footer --}}
            @if($invoice->notes || $invoice->terms || $invoice->footer)
            <div class="px-6 py-5 border-t border-gray-200 space-y-4">
                @if($invoice->notes)
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Notes</h3>
                    <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $invoice->notes }}</p>
                </div>
                @endif

                @if($invoice->terms)
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Terms & Conditions</h3>
                    <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $invoice->terms }}</p>
                </div>
                @endif

                @if($invoice->footer)
                <div>
                    <p class="text-sm text-gray-600 text-center whitespace-pre-wrap">{{ $invoice->footer }}</p>
                </div>
                @endif
            </div>
            @endif
        </div>

        {{-- Payment History --}}
        @if($invoice->payments->count() > 0)
        <div class="mt-6 bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Payment History</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transaction ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($invoice->payments as $payment)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $payment->payment_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-green-600">${{ number_format($payment->amount, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $payment->payment_method->label() }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $payment->transaction_id ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $payment->notes ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    {{-- Payment Modal --}}
    @if($showPaymentModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closePaymentModal"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div>
                    <div class="text-center sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            Record Payment
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Amount <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    wire:model="payment_amount"
                                    step="0.01"
                                    min="0.01"
                                    max="{{ $invoice->remaining_amount }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('payment_amount') border-red-300 @enderror"
                                >
                                @error('payment_amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Balance due: ${{ number_format($invoice->remaining_amount, 2) }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Payment Date <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    wire:model="payment_date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('payment_date') border-red-300 @enderror"
                                >
                                @error('payment_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Payment Method <span class="text-red-500">*</span>
                                </label>
                                <select
                                    wire:model="payment_method"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('payment_method') border-red-300 @enderror"
                                >
                                    <option value="">Select method</option>
                                    @foreach($paymentMethods as $method)
                                        <option value="{{ $method->value }}">{{ $method->label() }}</option>
                                    @endforeach
                                </select>
                                @error('payment_method')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Transaction ID</label>
                                <input
                                    type="text"
                                    wire:model="transaction_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Notes</label>
                                <textarea
                                    wire:model="payment_notes"
                                    rows="2"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                    <button
                        type="button"
                        wire:click="recordPayment"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm"
                    >
                        Record Payment
                    </button>
                    <button
                        type="button"
                        wire:click="closePaymentModal"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
