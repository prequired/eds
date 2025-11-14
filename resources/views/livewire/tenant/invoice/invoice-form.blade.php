<div class="py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Flash Messages --}}
        @if (session()->has('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        {{-- Form --}}
        <div class="space-y-6">
            {{-- Invoice Details --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Invoice Details</h2>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    {{-- Client --}}
                    <div class="sm:col-span-2">
                        <label for="client_id" class="block text-sm font-medium text-gray-700">
                            Client <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="client_id"
                            wire:model="client_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('client_id') border-red-300 @enderror"
                        >
                            <option value="">Select a client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}@if($client->company) - {{ $client->company }}@endif</option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Issue Date --}}
                    <div>
                        <label for="issue_date" class="block text-sm font-medium text-gray-700">
                            Issue Date <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="date"
                            id="issue_date"
                            wire:model="issue_date"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('issue_date') border-red-300 @enderror"
                        >
                        @error('issue_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Due Date --}}
                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700">
                            Due Date <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="date"
                            id="due_date"
                            wire:model="due_date"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('due_date') border-red-300 @enderror"
                        >
                        @error('due_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tax Rate --}}
                    <div>
                        <label for="tax_rate" class="block text-sm font-medium text-gray-700">
                            Tax Rate (%) <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            id="tax_rate"
                            wire:model.live="tax_rate"
                            step="0.01"
                            min="0"
                            max="100"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('tax_rate') border-red-300 @enderror"
                        >
                        @error('tax_rate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Line Items --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Line Items</h2>
                    <button
                        type="button"
                        wire:click="addItem"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Item
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Description
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                                    Quantity
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    Unit Price
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    Total
                                </th>
                                <th scope="col" class="px-3 py-3 w-12">
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($items as $index => $item)
                            <tr>
                                <td class="px-3 py-4">
                                    <input
                                        type="text"
                                        wire:model.live="items.{{ $index }}.description"
                                        placeholder="Description of service or product"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('items.' . $index . '.description') border-red-300 @enderror"
                                    >
                                    @error('items.' . $index . '.description')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                                <td class="px-3 py-4">
                                    <input
                                        type="number"
                                        wire:model.live="items.{{ $index }}.quantity"
                                        step="0.01"
                                        min="0"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('items.' . $index . '.quantity') border-red-300 @enderror"
                                    >
                                    @error('items.' . $index . '.quantity')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                                <td class="px-3 py-4">
                                    <input
                                        type="number"
                                        wire:model.live="items.{{ $index }}.unit_price"
                                        step="0.01"
                                        min="0"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('items.' . $index . '.unit_price') border-red-300 @enderror"
                                    >
                                    @error('items.' . $index . '.unit_price')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                                <td class="px-3 py-4 text-sm text-gray-900 font-medium">
                                    ${{ number_format($item['quantity'] * $item['unit_price'], 2) }}
                                </td>
                                <td class="px-3 py-4 text-center">
                                    @if(count($items) > 1)
                                    <button
                                        type="button"
                                        wire:click="removeItem({{ $index }})"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @error('items')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                {{-- Totals --}}
                <div class="mt-6 flex justify-end">
                    <div class="w-64 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium text-gray-900">${{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tax ({{ number_format($tax_rate, 2) }}%):</span>
                            <span class="font-medium text-gray-900">${{ number_format($tax, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2">
                            <span class="text-gray-900">Total:</span>
                            <span class="text-gray-900">${{ number_format($total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Additional Information --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h2>

                <div class="space-y-6">
                    {{-- Notes --}}
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea
                            id="notes"
                            wire:model="notes"
                            rows="3"
                            placeholder="Internal notes (not visible to client)"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('notes') border-red-300 @enderror"
                        ></textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Terms --}}
                    <div>
                        <label for="terms" class="block text-sm font-medium text-gray-700">Terms & Conditions</label>
                        <textarea
                            id="terms"
                            wire:model="terms"
                            rows="3"
                            placeholder="Payment terms and conditions"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('terms') border-red-300 @enderror"
                        ></textarea>
                        @error('terms')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Footer --}}
                    <div>
                        <label for="footer" class="block text-sm font-medium text-gray-700">Footer</label>
                        <textarea
                            id="footer"
                            wire:model="footer"
                            rows="2"
                            placeholder="Thank you message or additional information"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('footer') border-red-300 @enderror"
                        ></textarea>
                        @error('footer')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-between bg-white shadow-sm rounded-lg p-6">
                <a
                    href="{{ route('invoices.index') }}"
                    wire:navigate
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    Cancel
                </a>

                <div class="flex items-center space-x-3">
                    <button
                        type="button"
                        wire:click="saveDraft"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Save as Draft
                    </button>

                    @if(!$invoiceId)
                    <button
                        type="button"
                        wire:click="saveAndSend"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Save & Send
                    </button>
                    @else
                    <button
                        type="button"
                        wire:click="saveDraft"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Update Invoice
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
