<div class="max-w-2xl">
    <div class="bg-white rounded-lg shadow-sm">
        <form wire:submit="save">
            <div class="p-6 space-y-6">
                <!-- Header -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Send Invitation</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Invite a new team member to join your organization. They will receive an email with instructions to create their account.
                    </p>
                </div>

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="email"
                        id="email"
                        wire:model="email"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="colleague@example.com"
                        required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role Selection -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">
                        Role <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="role"
                        wire:model="role"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                        <option value="member">Member</option>
                        <option value="admin">Admin</option>
                        <option value="owner">Owner</option>
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <!-- Role Descriptions -->
                    <div class="mt-3 space-y-2">
                        <div class="text-sm">
                            <span class="font-medium text-gray-700">Member:</span>
                            <span class="text-gray-600">Can view clients/projects and manage tasks/time entries.</span>
                        </div>
                        <div class="text-sm">
                            <span class="font-medium text-gray-700">Admin:</span>
                            <span class="text-gray-600">Can manage clients, projects, websites, and tickets.</span>
                        </div>
                        <div class="text-sm">
                            <span class="font-medium text-gray-700">Owner:</span>
                            <span class="text-gray-600">Full access to everything including team and billing.</span>
                        </div>
                    </div>
                </div>

                <!-- Invitation Details -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Invitation Details</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>The invitation will be valid for 7 days</li>
                                    <li>An email will be sent with a secure link to accept the invitation</li>
                                    <li>They'll be able to set their own password when accepting</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="bg-gray-50 px-6 py-4 flex items-center justify-between rounded-b-lg">
                <a
                    href="{{ route('team.index') }}"
                    wire:navigate
                    class="text-sm font-medium text-gray-700 hover:text-gray-900">
                    Cancel
                </a>
                <button
                    type="submit"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Send Invitation
                </button>
            </div>
        </form>
    </div>
</div>
