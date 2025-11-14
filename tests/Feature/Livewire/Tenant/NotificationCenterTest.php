<?php

declare(strict_types=1);

use App\Livewire\Tenant\NotificationCenter;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

it('displays the notification bell', function () {
    Livewire::test(NotificationCenter::class)
        ->assertSee('Notifications');
});

it('displays unread count badge when there are unread notifications', function () {
    // Create a test notification
    $this->user->notify(new class extends \Illuminate\Notifications\Notification {
        public function via($notifiable) {
            return ['database'];
        }

        public function toArray($notifiable) {
            return ['message' => 'Test notification'];
        }
    });

    Livewire::test(NotificationCenter::class)
        ->assertSee('1');
});

it('does not display badge when there are no unread notifications', function () {
    Livewire::test(NotificationCenter::class)
        ->assertDontSee('class="absolute top-1 right-1');
});

it('shows notifications in dropdown', function () {
    $this->user->notify(new class extends \Illuminate\Notifications\Notification {
        public function via($notifiable) {
            return ['database'];
        }

        public function toArray($notifiable) {
            return ['message' => 'Important notification'];
        }
    });

    Livewire::test(NotificationCenter::class)
        ->call('toggleDropdown')
        ->assertSee('Important notification');
});

it('marks notification as read', function () {
    $this->user->notify(new class extends \Illuminate\Notifications\Notification {
        public function via($notifiable) {
            return ['database'];
        }

        public function toArray($notifiable) {
            return ['message' => 'Test notification'];
        }
    });

    $notification = $this->user->unreadNotifications()->first();

    Livewire::test(NotificationCenter::class)
        ->call('markAsRead', $notification->id)
        ->assertDispatched('notification-read');

    expect($this->user->unreadNotifications()->count())->toBe(0);
});

it('marks all notifications as read', function () {
    for ($i = 0; $i < 3; $i++) {
        $this->user->notify(new class extends \Illuminate\Notifications\Notification {
            public function via($notifiable) {
                return ['database'];
            }

            public function toArray($notifiable) {
                return ['message' => 'Test notification'];
            }
        });
    }

    expect($this->user->unreadNotifications()->count())->toBe(3);

    Livewire::test(NotificationCenter::class)
        ->call('markAllAsRead')
        ->assertDispatched('all-notifications-read');

    expect($this->user->unreadNotifications()->count())->toBe(0);
});

it('deletes notification', function () {
    $this->user->notify(new class extends \Illuminate\Notifications\Notification {
        public function via($notifiable) {
            return ['database'];
        }

        public function toArray($notifiable) {
            return ['message' => 'Test notification'];
        }
    });

    $notification = $this->user->notifications()->first();
    $notificationId = $notification->id;

    Livewire::test(NotificationCenter::class)
        ->call('deleteNotification', $notificationId)
        ->assertDispatched('notification-deleted');

    expect($this->user->notifications()->where('id', $notificationId)->exists())->toBeFalse();
});

it('limits notifications to 10 in dropdown', function () {
    for ($i = 0; $i < 15; $i++) {
        $this->user->notify(new class extends \Illuminate\Notifications\Notification {
            public function via($notifiable) {
                return ['database'];
            }

            public function toArray($notifiable) {
                return ['message' => 'Test notification ' . $i];
            }
        });
    }

    $component = Livewire::test(NotificationCenter::class);

    expect($component->get('notifications')->count())->toBe(10);
});

it('displays action URL when present', function () {
    $this->user->notify(new class extends \Illuminate\Notifications\Notification {
        public function via($notifiable) {
            return ['database'];
        }

        public function toArray($notifiable) {
            return [
                'message' => 'Test notification',
                'action_url' => '/test-url',
            ];
        }
    });

    Livewire::test(NotificationCenter::class)
        ->call('toggleDropdown')
        ->assertSee('/test-url')
        ->assertSee('View Details');
});

it('refreshes on notification-created event', function () {
    $component = Livewire::test(NotificationCenter::class);

    $this->user->notify(new class extends \Illuminate\Notifications\Notification {
        public function via($notifiable) {
            return ['database'];
        }

        public function toArray($notifiable) {
            return ['message' => 'New notification'];
        }
    });

    $component->dispatch('notification-created');

    expect($component->get('notifications')->count())->toBeGreaterThan(0);
});
