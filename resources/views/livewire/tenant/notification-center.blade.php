<div class="relative" x-data="{ show: @entangle('showDropdown') }">
    <!-- Notification Bell -->
    <button
        @click="show = !show"
        class="relative p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors"
        aria-label="Notifications"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        <!-- Badge -->
        @if($this->unreadCount > 0)
            <span class="absolute top-1 right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-600 text-xs font-bold text-white">
                {{ $this->unreadCount > 9 ? '9+' : $this->unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown -->
    <div
        x-show="show"
        @click.away="show = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50"
        style="display: none;"
    >
        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
            @if($this->unreadCount > 0)
                <button
                    wire:click="markAllAsRead"
                    class="text-xs text-blue-600 hover:text-blue-800 font-medium"
                >
                    Mark all as read
                </button>
            @endif
        </div>

        <!-- Notification List -->
        <div class="max-h-96 overflow-y-auto">
            @forelse($this->notifications as $notification)
                <div
                    class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition-colors {{ is_null($notification->read_at) ? 'bg-blue-50' : '' }}"
                >
                    <div class="flex items-start space-x-3">
                        <!-- Icon -->
                        <div class="flex-shrink-0 mt-1">
                            @php
                                $iconColor = is_null($notification->read_at) ? 'text-blue-600' : 'text-gray-400';
                            @endphp
                            <svg class="w-5 h-5 {{ $iconColor }}" fill="currentColor" viewBox="0 0 20 20">
                                <circle cx="10" cy="10" r="8" />
                            </svg>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">
                                {{ $notification->data['message'] ?? 'New notification' }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>

                            <!-- Actions -->
                            @if(isset($notification->data['action_url']))
                                <a
                                    href="{{ $notification->data['action_url'] }}"
                                    class="inline-block text-xs text-blue-600 hover:text-blue-800 mt-2 font-medium"
                                    @click="show = false"
                                >
                                    View Details →
                                </a>
                            @endif
                        </div>

                        <!-- Mark as read / Delete -->
                        <div class="flex-shrink-0 flex items-center space-x-2">
                            @if(is_null($notification->read_at))
                                <button
                                    wire:click="markAsRead('{{ $notification->id }}')"
                                    class="text-gray-400 hover:text-gray-600"
                                    title="Mark as read"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            @endif
                            <button
                                wire:click="deleteNotification('{{ $notification->id }}')"
                                class="text-gray-400 hover:text-red-600"
                                title="Delete"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <p class="text-sm text-gray-500">No notifications yet</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        @if($this->notifications->count() > 0)
            <div class="px-4 py-3 bg-gray-50 text-center border-t border-gray-200">
                <a
                    href="{{ route('notifications.index') }}"
                    class="text-sm text-blue-600 hover:text-blue-800 font-medium"
                    @click="show = false"
                >
                    View all notifications
                </a>
            </div>
        @endif
    </div>
</div>
