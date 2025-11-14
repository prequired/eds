<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Notifications</h2>
                <p class="text-sm text-gray-600 mt-1">Stay updated with your latest activities</p>
            </div>
            @if(auth()->user()->unreadNotifications()->count() > 0)
                <button
                    wire:click="markAllAsRead"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors"
                >
                    Mark all as read
                </button>
            @endif
        </div>

        <!-- Filters -->
        <div class="flex space-x-2 mt-4">
            <button
                wire:click="$set('filter', 'all')"
                class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $filter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
            >
                All
            </button>
            <button
                wire:click="$set('filter', 'unread')"
                class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $filter === 'unread' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
            >
                Unread
                @if(auth()->user()->unreadNotifications()->count() > 0)
                    <span class="ml-2 px-2 py-0.5 text-xs font-bold rounded-full {{ $filter === 'unread' ? 'bg-white text-blue-600' : 'bg-blue-600 text-white' }}">
                        {{ auth()->user()->unreadNotifications()->count() }}
                    </span>
                @endif
            </button>
            <button
                wire:click="$set('filter', 'read')"
                class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $filter === 'read' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
            >
                Read
            </button>
        </div>
    </div>

    <!-- Notifications -->
    <div class="space-y-3">
        @forelse($this->notifications as $notification)
            <div class="bg-white rounded-lg shadow-sm overflow-hidden {{ is_null($notification->read_at) ? 'border-l-4 border-blue-600' : '' }}">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <!-- Content -->
                        <div class="flex-1">
                            <div class="flex items-start space-x-3">
                                <!-- Icon -->
                                <div class="flex-shrink-0 mt-1">
                                    @if(is_null($notification->read_at))
                                        <div class="w-3 h-3 bg-blue-600 rounded-full"></div>
                                    @else
                                        <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                                    @endif
                                </div>

                                <!-- Message -->
                                <div class="flex-1">
                                    <p class="text-base text-gray-900">
                                        {{ $notification->data['message'] ?? 'New notification' }}
                                    </p>

                                    @if(isset($notification->data['details']))
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ $notification->data['details'] }}
                                        </p>
                                    @endif

                                    <div class="flex items-center mt-2 space-x-4">
                                        <span class="text-xs text-gray-500">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>

                                        @if(isset($notification->data['action_url']))
                                            <a
                                                href="{{ $notification->data['action_url'] }}"
                                                class="text-xs text-blue-600 hover:text-blue-800 font-medium"
                                            >
                                                View Details →
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex-shrink-0 ml-4 flex items-center space-x-2">
                            @if(is_null($notification->read_at))
                                <button
                                    wire:click="markAsRead('{{ $notification->id }}')"
                                    class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors"
                                    title="Mark as read"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            @endif
                            <button
                                wire:click="deleteNotification('{{ $notification->id }}')"
                                class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-gray-100 transition-colors"
                                title="Delete"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications</h3>
                <p class="text-sm text-gray-500">
                    @if($filter === 'unread')
                        You have no unread notifications.
                    @elseif($filter === 'read')
                        You have no read notifications.
                    @else
                        You don't have any notifications yet.
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($this->notifications->hasPages())
        <div class="mt-6">
            {{ $this->notifications->links() }}
        </div>
    @endif

    <!-- Bulk Actions -->
    @if($filter === 'read' && auth()->user()->readNotifications()->count() > 0)
        <div class="mt-6 bg-white rounded-lg shadow-sm p-4 text-center">
            <button
                wire:click="deleteAllRead"
                wire:confirm="Are you sure you want to delete all read notifications?"
                class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 rounded-lg hover:bg-red-50 transition-colors"
            >
                Delete all read notifications
            </button>
        </div>
    @endif
</div>
