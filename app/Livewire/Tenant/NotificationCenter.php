<?php

declare(strict_types=1);

namespace App\Livewire\Tenant;

use Illuminate\Contracts\View\View;
use Illuminate\Notifications\DatabaseNotification;
use Livewire\Attributes\On;
use Livewire\Component;

class NotificationCenter extends Component
{
    public bool $showDropdown = false;

    public function mount(): void
    {
        //
    }

    public function toggleDropdown(): void
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function markAsRead(string $notificationId): void
    {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $notificationId)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            $this->dispatch('notification-read');
        }
    }

    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
        $this->dispatch('all-notifications-read');
    }

    public function deleteNotification(string $notificationId): void
    {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $notificationId)
            ->first();

        if ($notification) {
            $notification->delete();
            $this->dispatch('notification-deleted');
        }
    }

    #[On('notification-created')]
    public function refreshNotifications(): void
    {
        // This will cause the component to re-render
    }

    public function getUnreadCountProperty(): int
    {
        return auth()->user()->unreadNotifications()->count();
    }

    public function getNotificationsProperty()
    {
        return auth()->user()
            ->notifications()
            ->latest()
            ->limit(10)
            ->get();
    }

    public function render(): View
    {
        return view('livewire.tenant.notification-center');
    }
}
