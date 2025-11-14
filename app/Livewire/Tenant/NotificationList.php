<?php

declare(strict_types=1);

namespace App\Livewire\Tenant;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class NotificationList extends Component
{
    use WithPagination;

    #[Url]
    public string $filter = 'all'; // all, unread, read

    public function mount(): void
    {
        //
    }

    public function markAsRead(string $notificationId): void
    {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $notificationId)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            $this->dispatch('notification', [
                'type' => 'success',
                'message' => 'Notification marked as read.',
            ]);
        }
    }

    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
        $this->dispatch('notification', [
            'type' => 'success',
            'message' => 'All notifications marked as read.',
        ]);
    }

    public function deleteNotification(string $notificationId): void
    {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $notificationId)
            ->first();

        if ($notification) {
            $notification->delete();
            $this->dispatch('notification', [
                'type' => 'success',
                'message' => 'Notification deleted.',
            ]);
        }
    }

    public function deleteAllRead(): void
    {
        auth()->user()
            ->readNotifications()
            ->delete();

        $this->dispatch('notification', [
            'type' => 'success',
            'message' => 'All read notifications deleted.',
        ]);
    }

    public function getNotificationsProperty()
    {
        $query = auth()->user()->notifications();

        if ($this->filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($this->filter === 'read') {
            $query->whereNotNull('read_at');
        }

        return $query->latest()->paginate(20);
    }

    public function render(): View
    {
        return view('livewire.tenant.notification-list')
            ->layout('layouts.tenant', ['header' => 'Notifications']);
    }
}
