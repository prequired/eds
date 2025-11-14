<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Websites</h1>
            <p class="mt-1 text-sm text-gray-500">Monitor and manage client websites</p>
        </div>
        <div class="flex items-center space-x-3">
            <button
                wire:click="$toggle('showFilters')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Filters
            </button>
            <a
                href="{{ route('websites.create') }}"
                wire:navigate
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Website
            </a>
        </div>
    </div>

    <!-- Filters (Collapsible) -->
    @if($showFilters)
    <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select wire:model.live="status" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">All Websites</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="suspended">Suspended</option>
                    <option value="archived">Archived</option>
                </select>
            </div>

            <!-- Environment Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Environment</label>
                <select wire:model.live="environment" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">All Environments</option>
                    <option value="production">Production</option>
                    <option value="staging">Staging</option>
                    <option value="development">Development</option>
                </select>
            </div>

            <!-- Uptime Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Uptime</label>
                <select wire:model.live="uptimeStatus" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">All Status</option>
                    <option value="up">Up</option>
                    <option value="down">Down</option>
                    <option value="unknown">Unknown</option>
                </select>
            </div>

            <!-- Sort Direction -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sort</label>
                <select wire:model.live="sortDirection" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="desc">Newest First</option>
                    <option value="asc">Oldest First</option>
                </select>
            </div>
        </div>
    </div>
    @endif

    <!-- Search Bar -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="relative">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search websites by name, URL, or client..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
            <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
    </div>

    @if($websites->count() > 0)
    <!-- Websites Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($websites as $website)
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow duration-200">
            <div class="p-6">
                <!-- Website Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 truncate">
                            {{ $website->name }}
                        </h3>
                        <a href="{{ $website->url }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 truncate block mt-1">
                            {{ $website->url }}
                            <svg class="inline w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                        <p class="text-xs text-gray-500 mt-1">
                            <span class="font-medium">Client:</span> {{ $website->client->name }}
                        </p>
                    </div>
                </div>

                <!-- Badges -->
                <div class="flex flex-wrap gap-2 mb-4">
                    <!-- Uptime Status -->
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $website->uptime_status->color() }}-100 text-{{ $website->uptime_status->color() }}-800">
                        @if($website->uptime_status->value === 'up')
                            <span class="w-2 h-2 mr-1 bg-{{ $website->uptime_status->color() }}-600 rounded-full"></span>
                        @endif
                        {{ $website->uptime_status->label() }}
                    </span>

                    <!-- Environment -->
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $website->environment->color() }}-100 text-{{ $website->environment->color() }}-800">
                        {{ $website->environment->label() }}
                    </span>

                    <!-- Status -->
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $website->status->color() }}-100 text-{{ $website->status->color() }}-800">
                        {{ $website->status->label() }}
                    </span>
                </div>

                <!-- Metadata -->
                <div class="space-y-2 text-sm text-gray-600">
                    @if($website->response_time_ms)
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span class="text-xs">{{ $website->response_time_ms }}ms response</span>
                    </div>
                    @endif

                    @if($website->last_checked_at)
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-xs">Checked {{ $website->last_checked_at->diffForHumans() }}</span>
                    </div>
                    @endif

                    @if($website->last_deployed_at)
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <span class="text-xs">Deployed {{ $website->last_deployed_at->diffForHumans() }}</span>
                    </div>
                    @endif
                </div>

                <!-- Performance Scores -->
                @if($website->lighthouse_performance || $website->lighthouse_accessibility || $website->lighthouse_seo)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-xs font-medium text-gray-700 mb-2">Lighthouse Scores</p>
                    <div class="flex items-center space-x-4 text-xs">
                        @if($website->lighthouse_performance)
                        <div class="flex items-center">
                            <span class="text-gray-500">Perf:</span>
                            <span class="ml-1 font-semibold {{ $website->lighthouse_performance >= 90 ? 'text-green-600' : ($website->lighthouse_performance >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $website->lighthouse_performance }}
                            </span>
                        </div>
                        @endif
                        @if($website->lighthouse_accessibility)
                        <div class="flex items-center">
                            <span class="text-gray-500">A11y:</span>
                            <span class="ml-1 font-semibold {{ $website->lighthouse_accessibility >= 90 ? 'text-green-600' : ($website->lighthouse_accessibility >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $website->lighthouse_accessibility }}
                            </span>
                        </div>
                        @endif
                        @if($website->lighthouse_seo)
                        <div class="flex items-center">
                            <span class="text-gray-500">SEO:</span>
                            <span class="ml-1 font-semibold {{ $website->lighthouse_seo >= 90 ? 'text-green-600' : ($website->lighthouse_seo >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $website->lighthouse_seo }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Actions -->
                <div class="mt-4 pt-4 border-t border-gray-200 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <a
                            href="{{ route('websites.edit', $website) }}"
                            wire:navigate
                            class="text-sm font-medium text-blue-600 hover:text-blue-800"
                        >
                            Edit
                        </a>
                        <span class="text-sm font-medium text-gray-400 cursor-pointer">
                            View Details →
                        </span>
                    </div>
                    <button
                        wire:click="archive('{{ $website->id }}')"
                        wire:confirm="Are you sure you want to archive this website?"
                        class="text-sm text-gray-500 hover:text-red-600">
                        Archive
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $websites->links() }}
    </div>
    @else
    <!-- Empty State -->
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No websites found</h3>
        <p class="mt-1 text-sm text-gray-500">
            @if($search)
                No websites match your search "{{ $search }}".
            @else
                Get started by adding your first website.
            @endif
        </p>
        @if(!$search)
        <div class="mt-6">
            <a
                href="{{ route('websites.create') }}"
                wire:navigate
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Your First Website
            </a>
        </div>
        @endif
    </div>
    @endif
</div>
