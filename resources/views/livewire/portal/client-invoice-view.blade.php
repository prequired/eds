<div>
    <!-- Header with Back Button -->
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('portal.invoices') }}" class="mr-4 text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Invoice {{ $invoice->invoice_number }}</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Issued {{ $invoice->invoice_date->format('F j, Y') }}
                </p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            @if($this->canPay())
                <button
                    wire:click="openPaymentModal"
                    class="px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors"
                >
                    Pay Now
                </button>
            @endif
            <button
                wire:click="downloadPdf"
                class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors"
            >
                Download PDF
            </button>
        </div>
    </div>

    <!-- Invoice Status Alert -->
    @if($invoice->status === \App\Enums\InvoiceStatus::OVERDUE)
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
            <div class="flex">
                <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-red-800">This invoice is overdue</h3>
                    <p class="mt-1 text-sm text-red-700">
                        Payment was due on {{ $invoice->due_date->format('F j, Y') }}. Please pay as soon as possible.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Invoice Details Card -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
        <!-- Invoice Header -->
        <div class="px-8 py-6 bg-gradient-to-r from-blue-600 to-blue-700 text-white">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold mb-2">{{ tenancy()->tenant->company_name }}</h2>
                    <p class="text-blue-100 text-sm">Invoice #{{ $invoice->invoice_number }}</p>
                </div>
                <div class="text-right">
                    <span class="px-3 py-1 bg-white bg-opacity-25 rounded-full text-sm font-medium">
                        {{ $invoice->status->label() }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Bill To / Due Date -->
        <div class="px-8 py-6 grid grid-cols-2 gap-8 border-b border-gray-200">
            <div>
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Bill To</h3>
                <p class="font-semibold text-gray-900">{{ auth('client')->user()->company ?: auth('client')->user()->name }}</p>
                @if(auth('client')->user()->address_line1)
                    <p class="text-sm text-gray-600 mt-1">{{ auth('client')->user()->address_line1 }}</p>
                    @if(auth('client')->user()->address_line2)
                        <p class="text-sm text-gray-600">{{ auth('client')->user()->address_line2 }}</p>
                    @endif
                    <p class="text-sm text-gray-600">
                        {{ auth('client')->user()->city }}@if(auth('client')->user()->state), {{ auth('client')->user()->state }}@endif {{ auth('client')->user()->postal_code }}
                    </p>
                @endif
                <p class="text-sm text-gray-600 mt-1">{{ auth('client')->user()->email }}</p>
            </div>

            <div class="text-right">
                <div class="mb-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Invoice Date</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $invoice->invoice_date->format('F j, Y') }}</p>
                </div>
                <div class="mb-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Due Date</p>
                    <p class="text-lg font-semibold {{ $invoice->status === \App\Enums\InvoiceStatus::OVERDUE ? 'text-red-600' : 'text-gray-900' }}">
                        {{ $invoice->due_date->format('F j, Y') }}
                    </p>
                </div>
                @if($invoice->notes)
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Notes</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $invoice->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Line Items -->
        <div class="px-8 py-6">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="pb-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="pb-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="pb-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Rate</th>
                        <th class="pb-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($invoice->items as $item)
                        <tr>
                            <td class="py-4 text-sm text-gray-900">
                                {{ $item->description }}
                            </td>
                            <td class="py-4 text-right text-sm text-gray-600">
                                {{ $item->quantity }}
                            </td>
                            <td class="py-4 text-right text-sm text-gray-600">
                                ${{ number_format($item->unit_price, 2) }}
                            </td>
                            <td class="py-4 text-right text-sm font-semibold text-gray-900">
                                ${{ number_format($item->total, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="px-8 py-6 bg-gray-50 border-t border-gray-200">
            <div class="max-w-md ml-auto space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="font-semibold text-gray-900">${{ number_format($invoice->subtotal, 2) }}</span>
                </div>

                @if($invoice->tax_rate > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Tax ({{ $invoice->tax_rate }}%):</span>
                        <span class="font-semibold text-gray-900">${{ number_format($invoice->tax_amount, 2) }}</span>
                    </div>
                @endif

                <div class="flex justify-between text-lg font-bold border-t border-gray-300 pt-2 mt-2">
                    <span class="text-gray-900">Total:</span>
                    <span class="text-gray-900">${{ number_format($invoice->total_amount, 2) }}</span>
                </div>

                @if($invoice->amount_paid > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Amount Paid:</span>
                        <span class="font-semibold text-green-600">-${{ number_format($invoice->amount_paid, 2) }}</span>
                    </div>
                @endif

                @if($invoice->balance_due > 0)
                    <div class="flex justify-between text-xl font-bold text-orange-600 border-t border-gray-300 pt-2 mt-2">
                        <span>Balance Due:</span>
                        <span>${{ number_format($invoice->balance_due, 2) }}</span>
                    </div>
                @else
                    <div class="flex justify-between text-lg font-bold text-green-600 border-t border-gray-300 pt-2 mt-2">
                        <span>✓ Paid in Full</span>
                        <span>${{ number_format($invoice->total_amount, 2) }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Payment History -->
    @if($invoice->payments->count() > 0)
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Payment History</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($invoice->payments as $payment)
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">
                                ${{ number_format($payment->amount, 2) }} - {{ $payment->payment_method }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                Received on {{ $payment->payment_date->format('F j, Y') }}
                            </p>
                            @if($payment->notes)
                                <p class="text-xs text-gray-600 mt-1">{{ $payment->notes }}</p>
                            @endif
                        </div>
                        <div class="flex items-center text-green-600">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm font-medium">Paid</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Payment Modal (Placeholder for Stripe Integration) -->
    @if($showPaymentModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click.self="closePaymentModal">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Pay Invoice</h3>
                    <button wire:click="closePaymentModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="mb-6">
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($invoice->balance_due, 2) }}</p>
                    <p class="text-sm text-gray-600">Amount due</p>
                </div>

                <!-- Stripe Payment Form Will Go Here -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-blue-800">
                        <strong>Coming Soon:</strong> Online payment processing with Stripe will be available shortly.
                    </p>
                </div>

                <div class="flex space-x-3">
                    <button
                        wire:click="closePaymentModal"
                        class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
