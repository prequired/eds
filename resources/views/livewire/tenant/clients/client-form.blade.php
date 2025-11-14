<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
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
        <form wire:submit.prevent="save" class="space-y-6">
            {{-- Basic Information --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    {{-- Name --}}
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            wire:model="name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('name') border-red-300 @enderror"
                        >
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input
                            type="email"
                            id="email"
                            wire:model="email"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('email') border-red-300 @enderror"
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                        <input
                            type="tel"
                            id="phone"
                            wire:model="phone"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('phone') border-red-300 @enderror"
                        >
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Company --}}
                    <div>
                        <label for="company" class="block text-sm font-medium text-gray-700">Company</label>
                        <input
                            type="text"
                            id="company"
                            wire:model="company"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('company') border-red-300 @enderror"
                        >
                        @error('company')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Website --}}
                    <div>
                        <label for="website" class="block text-sm font-medium text-gray-700">Website</label>
                        <input
                            type="url"
                            id="website"
                            wire:model="website"
                            placeholder="https://example.com"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('website') border-red-300 @enderror"
                        >
                        @error('website')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Address Information --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Address Information</h2>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    {{-- Address --}}
                    <div class="sm:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700">Street Address</label>
                        <input
                            type="text"
                            id="address"
                            wire:model="address"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('address') border-red-300 @enderror"
                        >
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- City --}}
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                        <input
                            type="text"
                            id="city"
                            wire:model="city"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('city') border-red-300 @enderror"
                        >
                        @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- State --}}
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700">State / Province</label>
                        <input
                            type="text"
                            id="state"
                            wire:model="state"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('state') border-red-300 @enderror"
                        >
                        @error('state')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ZIP --}}
                    <div>
                        <label for="zip" class="block text-sm font-medium text-gray-700">ZIP / Postal Code</label>
                        <input
                            type="text"
                            id="zip"
                            wire:model="zip"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('zip') border-red-300 @enderror"
                        >
                        @error('zip')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Country --}}
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                        <input
                            type="text"
                            id="country"
                            wire:model="country"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('country') border-red-300 @enderror"
                        >
                        @error('country')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Billing Information --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Billing Information</h2>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    {{-- Hourly Rate --}}
                    <div>
                        <label for="hourly_rate" class="block text-sm font-medium text-gray-700">Hourly Rate</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input
                                type="number"
                                id="hourly_rate"
                                wire:model="hourly_rate"
                                min="0"
                                step="1"
                                class="block w-full pl-7 pr-12 rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('hourly_rate') border-red-300 @enderror"
                            >
                        </div>
                        @error('hourly_rate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Currency --}}
                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700">Currency</label>
                        <select
                            id="currency"
                            wire:model="currency"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('currency') border-red-300 @enderror"
                        >
                            <option value="USD">USD - US Dollar</option>
                            <option value="EUR">EUR - Euro</option>
                            <option value="GBP">GBP - British Pound</option>
                            <option value="CAD">CAD - Canadian Dollar</option>
                        </select>
                        @error('currency')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Billing Email --}}
                    <div>
                        <label for="billing_email" class="block text-sm font-medium text-gray-700">Billing Email</label>
                        <input
                            type="email"
                            id="billing_email"
                            wire:model="billing_email"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('billing_email') border-red-300 @enderror"
                        >
                        @error('billing_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Billing Name --}}
                    <div>
                        <label for="billing_name" class="block text-sm font-medium text-gray-700">Billing Name</label>
                        <input
                            type="text"
                            id="billing_name"
                            wire:model="billing_name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('billing_name') border-red-300 @enderror"
                        >
                        @error('billing_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Billing Address --}}
                    <div class="sm:col-span-2">
                        <label for="billing_address" class="block text-sm font-medium text-gray-700">Billing Address</label>
                        <textarea
                            id="billing_address"
                            wire:model="billing_address"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('billing_address') border-red-300 @enderror"
                        ></textarea>
                        @error('billing_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Additional Notes</h2>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea
                        id="notes"
                        wire:model="notes"
                        rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('notes') border-red-300 @enderror"
                        placeholder="Add any additional notes or comments about this client..."
                    ></textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex justify-end space-x-3 bg-gray-50 px-4 py-3 rounded-lg">
                <a
                    href="{{ route('clients.index') }}"
                    wire:navigate
                    class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Cancel
                </a>
                <button
                    type="submit"
                    class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    {{ $clientId ? 'Update Client' : 'Create Client' }}
                </button>
            </div>
        </form>
    </div>
</div>
