<div class="space-y-6">
    {{-- Header with Stats --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500">Total Entries</h3>
                <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $entries->total() }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Total Hours</h3>
                <p class="mt-2 text-3xl font-semibold text-gray-900">{{ number_format($totalHours, 2) }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Total Amount</h3>
                <p class="mt-2 text-3xl font-semibold text-gray-900">${{ number_format($totalAmount, 2) }}</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            {{-- Search --}}
            <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input
                    type="text"
                    id="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by description..."
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                >
            </div>

            {{-- Project Filter --}}
            <div>
                <label for="project-filter" class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                <select
                    id="project-filter"
                    wire:model.live="projectFilter"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                >
                    <option value="all">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Client Filter --}}
            <div>
                <label for="client-filter" class="block text-sm font-medium text-gray-700 mb-1">Client</label>
                <select
                    id="client-filter"
                    wire:model.live="clientFilter"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                >
                    <option value="all">All Clients</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Billable Filter --}}
            <div>
                <label for="billable-filter" class="block text-sm font-medium text-gray-700 mb-1">Billable</label>
                <select
                    id="billable-filter"
                    wire:model.live="billableFilter"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                >
                    <option value="all">All</option>
                    <option value="yes">Billable</option>
                    <option value="no">Non-Billable</option>
                </select>
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-5 gap-4">
            {{-- Status Filter --}}
            <div>
                <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select
                    id="status-filter"
                    wire:model.live="statusFilter"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                >
                    <option value="all">All Statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Clear Filters --}}
            <div class="flex items-end">
                <button
                    type="button"
                    wire:click="$set('search', ''); $set('projectFilter', 'all'); $set('clientFilter', 'all'); $set('billableFilter', 'all'); $set('statusFilter', 'all')"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Clear Filters
                </button>
            </div>
        </div>
    </div>

    {{-- Time Entries Table --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($entries->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            @if(auth()->user()->isOwner() || auth()->user()->isAdmin())
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                            @endif
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Project
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Client
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Start Time
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Duration
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Rate
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($entries as $entry)
                            <tr class="hover:bg-gray-50">
                                @if(auth()->user()->isOwner() || auth()->user()->isAdmin())
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $entry->user->name }}
                                    </td>
                                @endif
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="max-w-xs truncate" title="{{ $entry->description }}">
                                        {{ $entry->description }}
                                    </div>
                                    @if($entry->notes)
                                        <div class="text-xs text-gray-500 max-w-xs truncate" title="{{ $entry->notes }}">
                                            {{ $entry->notes }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($entry->project)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $entry->project->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($entry->client)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            {{ $entry->client->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>{{ $entry->start_time->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $entry->start_time->format('h:i A') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($entry->isRunning())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse mr-1.5"></span>
                                            Running
                                        </span>
                                    @else
                                        <div class="font-medium">{{ $entry->formatted_duration }}</div>
                                        <div class="text-xs text-gray-500">{{ number_format($entry->duration_in_hours, 2) }}h</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($entry->billable)
                                        @if($entry->hourly_rate)
                                            <div class="font-medium text-green-600">${{ number_format($entry->hourly_rate, 2) }}/hr</div>
                                            <div class="text-xs text-gray-500">Billable</div>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Billable
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">Non-billable</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    @if($entry->billable && $entry->hourly_rate && !$entry->isRunning())
                                        <div class="text-green-600">${{ number_format($entry->total_amount, 2) }}</div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $entry->status->color() }}-100 text-{{ $entry->status->color() }}-800">
                                        {{ $entry->status->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        @if(auth()->user()->can('time_entries.update') && ($entry->user_id === auth()->id() || auth()->user()->isOwner() || auth()->user()->isAdmin()))
                                            <button
                                                type="button"
                                                class="text-blue-600 hover:text-blue-900"
                                                title="Edit"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                        @endif

                                        @if(auth()->user()->can('time_entries.delete') && ($entry->user_id === auth()->id() || auth()->user()->isOwner() || auth()->user()->isAdmin()))
                                            <button
                                                type="button"
                                                wire:click="deleteEntry('{{ $entry->id }}')"
                                                wire:confirm="Are you sure you want to delete this time entry?"
                                                class="text-red-600 hover:text-red-900"
                                                title="Delete"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $entries->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No time entries found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if($search || $projectFilter !== 'all' || $clientFilter !== 'all' || $billableFilter !== 'all' || $statusFilter !== 'all')
                        Try adjusting your filters or clearing them.
                    @else
                        Get started by tracking your time.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>
