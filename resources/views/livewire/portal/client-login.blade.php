<div>
    <!-- Logo/Header -->
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-gray-900">{{ tenancy()->tenant->company_name }}</h1>
        <p class="mt-2 text-sm text-gray-600">Client Portal Login</p>
    </div>

    <!-- Login Form -->
    <form wire:submit="login">
        <!-- Email Address -->
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                Email Address
            </label>
            <input
                type="email"
                id="email"
                wire:model="email"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                required
                autofocus
            />
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                Password
            </label>
            <input
                type="password"
                id="password"
                wire:model="password"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                required
            />
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="mb-6">
            <label class="flex items-center">
                <input
                    type="checkbox"
                    wire:model="remember"
                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                />
                <span class="ml-2 text-sm text-gray-600">Remember me</span>
            </label>
        </div>

        <!-- Login Button -->
        <div>
            <button
                type="submit"
                class="w-full px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
            >
                Sign In
            </button>
        </div>
    </form>

    <!-- Help Text -->
    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600">
            Need help accessing your account?
            <br>
            Please contact {{ tenancy()->tenant->company_name }} for assistance.
        </p>
    </div>
</div>
