<x-tenant-layout>
    <x-slot name="header">
        Profile Settings
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="max-w-2xl">
                <livewire:profile.update-profile-information-form />
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="max-w-2xl">
                <livewire:profile.update-password-form />
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="max-w-2xl">
                <livewire:profile.delete-user-form />
            </div>
        </div>
    </div>
</x-tenant-layout>
