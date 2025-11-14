<?php

declare(strict_types=1);

namespace App\Livewire\Portal;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ClientLogin extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required')]
    public string $password = '';

    public bool $remember = false;

    public function mount(): void
    {
        // If already authenticated, redirect to portal dashboard
        if (Auth::guard('client')->check()) {
            $this->redirect(route('portal.dashboard'), navigate: true);
        }
    }

    public function login(): void
    {
        $this->validate();

        $client = \App\Models\Tenant\Client::where('email', $this->email)
            ->portalEnabled()
            ->first();

        if (!$client) {
            $this->addError('email', 'These credentials do not match our records.');
            return;
        }

        if (!Hash::check($this->password, $client->portal_password)) {
            $this->addError('email', 'These credentials do not match our records.');
            return;
        }

        // Update last login
        $client->update(['last_portal_login' => now()]);

        // Log the client in
        Auth::guard('client')->login($client, $this->remember);

        session()->regenerate();

        $this->redirect(route('portal.dashboard'), navigate: true);
    }

    #[Layout('layouts.portal')]
    public function render(): View
    {
        return view('livewire.portal.client-login');
    }
}
