<div class="bg-white rounded-lg shadow-sm p-4">
    @if($runningTimer)
        {{-- Running Timer Display --}}
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <div class="flex items-center justify-center w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                    <span class="text-sm font-medium text-gray-700">Timer Running</span>
                </div>
                <div class="text-2xl font-mono font-bold text-gray-900">
                    {{ $this->formattedElapsed }}
                </div>
            </div>

            <div class="text-sm text-gray-600">
                <p class="font-medium">{{ $description }}</p>
                @if($runningTimer->project)
                    <p class="text-xs text-gray-500">Project: {{ $runningTimer->project->name }}</p>
                @endif
                @if($runningTimer->client)
                    <p class="text-xs text-gray-500">Client: {{ $runningTimer->client->name }}</p>
                @endif
            </div>

            <button
                wire:click="stopTimer"
                class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" />
                </svg>
                Stop Timer
            </button>
        </div>

        {{-- Auto-refresh timer every second --}}
        <script>
            setInterval(() => {
                @this.call('refreshTimer');
            }, 1000);
        </script>
    @else
        {{-- Start Timer Form --}}
        <div class="space-y-3">
            <h3 class="text-sm font-medium text-gray-900">Start Timer</h3>

            <div>
                <input
                    type="text"
                    wire:model="description"
                    placeholder="What are you working on?"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm @error('description') border-red-300 @enderror"
                >
                @error('description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <select
                        wire:model="project_id"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    >
                        <option value="">No Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select
                        wire:model="client_id"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    >
                        <option value="">No Client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input
                        type="checkbox"
                        wire:model="billable"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                    <span class="ml-2 text-xs text-gray-600">Billable</span>
                </label>

                @if($billable)
                <div class="flex items-center">
                    <span class="text-xs text-gray-600 mr-1">$</span>
                    <input
                        type="number"
                        wire:model="hourly_rate"
                        placeholder="Rate"
                        step="0.01"
                        class="block w-20 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-xs"
                    >
                    <span class="ml-1 text-xs text-gray-600">/hr</span>
                </div>
                @endif
            </div>

            <button
                wire:click="startTimer"
                class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Start Timer
            </button>
        </div>
    @endif
</div>
