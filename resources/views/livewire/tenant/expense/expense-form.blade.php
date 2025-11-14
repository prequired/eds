<div>
    <div class="max-w-4xl mx-auto">
        <form wire:submit="save" class="space-y-6">
            {{-- Basic Information --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Expense Details</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Category --}}
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="category"
                            wire:model="category"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('category') border-red-300 @enderror"
                        >
                            <option value="">Select category...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->value }}">{{ $cat->icon() }} {{ $cat->label() }}</option>
                            @endforeach
                        </select>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Amount --}}
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                            Amount <span class="text-red-500">*</span>
                        </label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input
                                type="number"
                                id="amount"
                                wire:model="amount"
                                step="0.01"
                                min="0"
                                placeholder="0.00"
                                class="block w-full rounded-md border-gray-300 pl-7 focus:border-blue-500 focus:ring-blue-500 @error('amount') border-red-300 @enderror"
                            >
                        </div>
                        @error('amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Expense Date --}}
                    <div>
                        <label for="expense_date" class="block text-sm font-medium text-gray-700 mb-1">
                            Expense Date <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="date"
                            id="expense_date"
                            wire:model="expense_date"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('expense_date') border-red-300 @enderror"
                        >
                        @error('expense_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Billable --}}
                    <div class="flex items-center pt-7">
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                wire:model="billable"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                            <span class="ml-2 text-sm text-gray-700">Billable to client</span>
                        </label>
                    </div>
                </div>

                {{-- Description --}}
                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        id="description"
                        wire:model="description"
                        rows="3"
                        placeholder="Describe the expense..."
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-300 @enderror"
                    ></textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Project & Client --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Project & Client</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Project --}}
                    <div>
                        <label for="project_id" class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                        <select
                            id="project_id"
                            wire:model="project_id"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">No Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Client --}}
                    <div>
                        <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">Client</label>
                        <select
                            id="client_id"
                            wire:model="client_id"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">No Client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Receipt --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Receipt</h3>

                @if($existing_receipt_path && !$remove_receipt)
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="text-sm text-gray-700">Receipt attached</span>
                            </div>
                            <button
                                type="button"
                                wire:click="removeReceipt"
                                class="text-sm text-red-600 hover:text-red-700"
                            >
                                Remove
                            </button>
                        </div>
                    </div>
                @endif

                <div>
                    <label for="receipt" class="block text-sm font-medium text-gray-700 mb-1">
                        Upload Receipt
                    </label>
                    <input
                        type="file"
                        id="receipt"
                        wire:model="receipt"
                        accept=".jpg,.jpeg,.png,.pdf"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                    >
                    <p class="mt-1 text-xs text-gray-500">JPG, PNG, or PDF up to 5MB</p>
                    @error('receipt')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    @if($receipt)
                        <div class="mt-2 text-sm text-green-600">
                            File selected: {{ $receipt->getClientOriginalName() }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Notes --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Notes</h3>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea
                        id="notes"
                        wire:model="notes"
                        rows="3"
                        placeholder="Any additional notes..."
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('notes') border-red-300 @enderror"
                    ></textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end space-x-3">
                <a
                    href="{{ route('expenses.index') }}"
                    wire:navigate
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    Cancel
                </a>
                <button
                    type="submit"
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    @if($isEdit)
                        Update Expense
                    @else
                        Create Expense
                    @endif
                </button>
            </div>
        </form>
    </div>
</div>
