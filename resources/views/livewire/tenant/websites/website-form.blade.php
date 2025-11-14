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
                    {{-- Client --}}
                    <div class="sm:col-span-2">
                        <label for="client_id" class="block text-sm font-medium text-gray-700">
                            Client <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="client_id"
                            wire:model.live="client_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('client_id') border-red-300 @enderror"
                        >
                            <option value="">Select a client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Project (Optional) --}}
                    <div class="sm:col-span-2">
                        <label for="project_id" class="block text-sm font-medium text-gray-700">
                            Project (Optional)
                        </label>
                        <select
                            id="project_id"
                            wire:model="project_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('project_id') border-red-300 @enderror"
                            @if(!$client_id) disabled @endif
                        >
                            <option value="">No project (standalone website)</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if(!$client_id)
                            <p class="mt-1 text-sm text-gray-500">Select a client first to choose a project</p>
                        @endif
                    </div>

                    {{-- Name --}}
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Website Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            wire:model="name"
                            placeholder="e.g., Acme Corp Website"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('name') border-red-300 @enderror"
                        >
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- URL --}}
                    <div class="sm:col-span-2">
                        <label for="url" class="block text-sm font-medium text-gray-700">
                            URL <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="url"
                            id="url"
                            wire:model="url"
                            placeholder="https://example.com"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('url') border-red-300 @enderror"
                        >
                        @error('url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Environment --}}
                    <div>
                        <label for="environment" class="block text-sm font-medium text-gray-700">Environment</label>
                        <select
                            id="environment"
                            wire:model="environment"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('environment') border-red-300 @enderror"
                        >
                            <option value="">Select environment</option>
                            <option value="production">Production</option>
                            <option value="staging">Staging</option>
                            <option value="development">Development</option>
                        </select>
                        @error('environment')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select
                            id="status"
                            wire:model="status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('status') border-red-300 @enderror"
                        >
                            <option value="">Select status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="suspended">Suspended</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Server Details --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Server Details</h2>
                <p class="text-sm text-gray-600 mb-4">Optional information about where the website is hosted</p>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    {{-- Server Provider --}}
                    <div>
                        <label for="server_provider" class="block text-sm font-medium text-gray-700">Server Provider</label>
                        <select
                            id="server_provider"
                            wire:model="server_provider"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('server_provider') border-red-300 @enderror"
                        >
                            <option value="">Select provider</option>
                            <option value="digitalocean">DigitalOcean</option>
                            <option value="aws">AWS</option>
                            <option value="vultr">Vultr</option>
                            <option value="linode">Linode</option>
                            <option value="cloudways">Cloudways</option>
                            <option value="forge">Laravel Forge</option>
                            <option value="ploi">Ploi</option>
                            <option value="other">Other</option>
                        </select>
                        @error('server_provider')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Server ID --}}
                    <div>
                        <label for="server_id" class="block text-sm font-medium text-gray-700">Server ID</label>
                        <input
                            type="text"
                            id="server_id"
                            wire:model="server_id"
                            placeholder="e.g., droplet-123456"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('server_id') border-red-300 @enderror"
                        >
                        @error('server_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Server IP --}}
                    <div class="sm:col-span-2">
                        <label for="server_ip" class="block text-sm font-medium text-gray-700">Server IP Address</label>
                        <input
                            type="text"
                            id="server_ip"
                            wire:model="server_ip"
                            placeholder="e.g., 192.168.1.1"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('server_ip') border-red-300 @enderror"
                        >
                        @error('server_ip')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Repository Details --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Repository Details</h2>
                <p class="text-sm text-gray-600 mb-4">Optional information about the code repository</p>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    {{-- Repository Provider --}}
                    <div>
                        <label for="repository_provider" class="block text-sm font-medium text-gray-700">Repository Provider</label>
                        <select
                            id="repository_provider"
                            wire:model="repository_provider"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('repository_provider') border-red-300 @enderror"
                        >
                            <option value="">Select provider</option>
                            <option value="github">GitHub</option>
                            <option value="gitlab">GitLab</option>
                            <option value="bitbucket">Bitbucket</option>
                            <option value="other">Other</option>
                        </select>
                        @error('repository_provider')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Repository Branch --}}
                    <div>
                        <label for="repository_branch" class="block text-sm font-medium text-gray-700">Branch</label>
                        <input
                            type="text"
                            id="repository_branch"
                            wire:model="repository_branch"
                            placeholder="main"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('repository_branch') border-red-300 @enderror"
                        >
                        @error('repository_branch')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Repository URL --}}
                    <div class="sm:col-span-2">
                        <label for="repository_url" class="block text-sm font-medium text-gray-700">Repository URL</label>
                        <input
                            type="url"
                            id="repository_url"
                            wire:model="repository_url"
                            placeholder="https://github.com/username/repo"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('repository_url') border-red-300 @enderror"
                        >
                        @error('repository_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Deployment --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Deployment</h2>
                <p class="text-sm text-gray-600 mb-4">Optional deployment configuration</p>

                <div class="grid grid-cols-1 gap-6">
                    {{-- Deployment Method --}}
                    <div>
                        <label for="deployment_method" class="block text-sm font-medium text-gray-700">Deployment Method</label>
                        <select
                            id="deployment_method"
                            wire:model="deployment_method"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('deployment_method') border-red-300 @enderror"
                        >
                            <option value="">Select method</option>
                            <option value="forge">Laravel Forge</option>
                            <option value="github_actions">GitHub Actions</option>
                            <option value="gitlab_ci">GitLab CI</option>
                            <option value="bitbucket_pipelines">Bitbucket Pipelines</option>
                            <option value="manual">Manual</option>
                            <option value="other">Other</option>
                        </select>
                        @error('deployment_method')
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
                        placeholder="Add any additional notes about this website..."
                    ></textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex justify-end space-x-3 bg-gray-50 px-4 py-3 rounded-lg">
                <a
                    href="{{ route('websites.index') }}"
                    wire:navigate
                    class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Cancel
                </a>
                <button
                    type="submit"
                    class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    {{ $websiteId ? 'Update Website' : 'Create Website' }}
                </button>
            </div>
        </form>
    </div>
</div>
