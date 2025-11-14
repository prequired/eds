<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Client Portal' }} - {{ tenancy()->tenant->company_name }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50">
    @if(auth('client')->check())
        <!-- Client Portal Layout -->
        <div class="min-h-screen">
            <!-- Top Navigation Bar -->
            <nav class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <!-- Logo/Company Name -->
                        <div class="flex items-center">
                            <h1 class="text-xl font-bold text-gray-900">
                                {{ tenancy()->tenant->company_name }}
                            </h1>
                            <span class="ml-3 px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">
                                Client Portal
                            </span>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden sm:flex sm:items-center sm:space-x-8">
                            <a href="{{ route('portal.dashboard') }}"
                               class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('portal.dashboard') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-700 hover:text-gray-900' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('portal.invoices') }}"
                               class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('portal.invoices*') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-700 hover:text-gray-900' }}">
                                Invoices
                            </a>
                            <a href="{{ route('portal.profile') }}"
                               class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('portal.profile') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-700 hover:text-gray-900' }}">
                                Profile
                            </a>
                        </div>

                        <!-- User Menu -->
                        <div class="flex items-center" x-data="{ open: false }">
                            <div class="relative">
                                <button @click="open = !open" class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
                                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold mr-2">
                                        {{ strtoupper(substr(auth('client')->user()->name, 0, 1)) }}
                                    </div>
                                    <span>{{ auth('client')->user()->name }}</span>
                                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div x-show="open"
                                     @click.away="open = false"
                                     x-transition
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50"
                                     style="display: none;">
                                    <a href="{{ route('portal.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Profile
                                    </a>
                                    <form method="POST" action="{{ route('portal.logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {{ $slot }}
            </main>
        </div>
    @else
        <!-- Guest Layout (Login Page) -->
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    @endif

    @livewireScripts
    <script>
        // Listen for notification events
        window.addEventListener('notification', event => {
            const detail = event.detail[0] || event.detail;
            // You can integrate with a notification library here
            alert(`${detail.type.toUpperCase()}: ${detail.message}`);
        });
    </script>
</body>
</html>
