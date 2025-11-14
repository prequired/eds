<?php

use App\Actions\Tenant\Team\AcceptTeamInvitationAction;
use App\Data\Tenant\Team\AcceptTeamInvitationData;
use App\Models\Tenant\TeamInvitation;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $token = '';
    public ?TeamInvitation $invitation = null;
    public string $name = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $invalidInvitation = false;

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->invitation = TeamInvitation::where('token', $token)->first();

        if (!$this->invitation || $this->invitation->isExpired() || $this->invitation->isAccepted()) {
            $this->invalidInvitation = true;
        }
    }

    public function accept(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            $data = AcceptTeamInvitationData::from([
                'name' => $this->name,
                'password' => $this->password,
            ]);

            $action = new AcceptTeamInvitationAction();
            $user = $action($this->invitation, $data);

            // Log the user in
            Auth::login($user);

            $this->redirect(route('dashboard'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }
}; ?>

<div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        @if($invalidInvitation)
            <!-- Invalid Invitation -->
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <h2 class="mt-4 text-2xl font-bold text-gray-900">Invalid Invitation</h2>
                <p class="mt-2 text-sm text-gray-600">
                    This invitation link is either invalid, expired, or has already been accepted.
                </p>
                <div class="mt-6">
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Go to Login
                    </a>
                </div>
            </div>
        @else
            <!-- Accept Invitation Form -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900">Welcome!</h2>
                    <p class="mt-2 text-sm text-gray-600">
                        You've been invited to join <strong>{{ tenancy()->tenant?->company_name ?? 'the team' }}</strong>
                    </p>
                </div>

                @if (session('error'))
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit="accept" class="space-y-6">
                    <!-- Email (Read-only) -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email Address
                        </label>
                        <input
                            type="email"
                            id="email"
                            value="{{ $invitation->email }}"
                            disabled
                            class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm">
                    </div>

                    <!-- Role (Read-only) -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">
                            Your Role
                        </label>
                        <div class="mt-1">
                            <span class="px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 text-sm font-medium inline-block">
                                {{ $invitation->role->label() }}
                            </span>
                        </div>
                    </div>

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            wire:model="name"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="John Doe"
                            required
                            autofocus>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="password"
                            id="password"
                            wire:model="password"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="••••••••"
                            required>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="password"
                            id="password_confirmation"
                            wire:model="password_confirmation"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="••••••••"
                            required>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button
                            type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                            Accept Invitation & Create Account
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-500">
                        By accepting this invitation, you agree to our Terms of Service and Privacy Policy.
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>
