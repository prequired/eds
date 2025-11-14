<?php

declare(strict_types=1);

use App\Livewire\Tenant\NotificationList;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

it('renders successfully', function () {
    Livewire::test(NotificationList::class)
        ->assertStatus(200)
        ->assertSee('Notifications');
});

it('displays empty state when no notifications', function () {
    Livewire::test(NotificationList::class)
        ->assertSee('No notifications')
        ->assertSee("You don't have any notifications yet");
});

it('displays notifications list', function () {
    for ($i = 0; $i < 3; $i++) {
        $this->user->notify(new class extends \Illuminate\Notifications\Notification {
            public function via($notifiable) {
                return ['database'];
            }

            public function toArray($notifiable) {
                return ['message' => 'Test notification ' . uniqid()];
            }
        });
    }

    Livewire::test(NotificationList::class)
        ->assertSee('Test notification');
});

it('filters by unread notifications', function () {
    // Create unread notification
    $this->user->notify(new class extends \Illuminate\Notifications\Notification {
        public function via($notifiable) {
            return ['database'];
        }

        public function toArray($notifiable) {
            return ['message' => 'Unread notification'];
        }
    });

    // Create read notification
    $this->user->notify(new class extends \Illuminate\Notifications\Notification {
        public function via($notifiable) {
            return ['database'];
        }

        public function toArray($notifiable) {
            return ['message' => 'Read notification'];
        }
    });
    $this->user->notifications()->skip(1)->first()->markAsRead();

    Livewire::test(NotificationList::class)
        ->set('filter', 'unread')
        ->assertSee('Unread notification')
        ->assertDontSee('Read notification');
});

it('filters by read notifications', function () {
    // Create unread notification
    $this->user->notify(new class extends \Illuminate\Notifications\Notification {
        public function via($notifiable) {
            return ['database'];
        }

        public function toArray($notifiable) {
            return ['message' => 'Unread notification'];
        }
    });

    // Create read notification
    $this->user->notify(new class extends \Illuminate\Notifications\Notification {
        public function via($notifiable) {
            return ['database'];
        }

        public function toArray($notifiable) {
            return ['message' => 'Read notification'];
        }
    });
    $this->user->notifications()->skip(1)->first()->markAsRead();

    Livewire::test(NotificationList::class)
        ->set('filter', 'read')
        ->assertSee('Read notification')
        ->assertDontSee('Unread notification');
});

it('shows all notifications by default', function () {
    $this->user->notify(new class extends \Illuminate\Notifications\Notification {
        public function via($notifiable) {
            return ['database'];
        }

        public function toArray($notifiable) {
            return ['message' => 'Unread notification'];
        }
    });

    $this->user->notify(new class extends \Illuminate\Notifications\Notification {
        public function via($notifiable) {
            return ['database'];
        }

        public function toArray($notifiable) {
            return ['message' => 'Read notification'];
        }
    });
    $this->user->notifications()->skip(1)->first()->markAsRead();

    Livewire::test(NotificationList::class)
        ->set('filter', 'all')
        ->assertSee('Unread notification')
        ->assertSee('Read notification');
});

it('marks single notification as read', function () {
    $this->user->notify(new class extends \Illuminate\Notifications\Notification {
        public function via($notifiable) {
            return ['database'];
        }

        public function toArray($notifiable) {
            return ['message' => 'Test notification'];
        }
    });

    $notification = $this->user->unreadNotifications()->first();

    Livewire::test(NotificationList::class)
        ->call('markAsRead', $notification->id)
        ->assertDispatched('notification');

    expect($this->user->unreadNotifications()->count())->toBe(0);
});

it('marks all notifications as read', function () {
    for ($i = 0; $i < 5; $i++) {
        $this->user->notify(new class extends \Illuminate\Notifications\Notification {
            public function via($notifiable) {
                return ['database'];
            }

            public function toArray($notifiable) {
                return ['message' => 'Test notification'];
            }
        });
    }

    expect($this->user->unreadNotifications()->count())->toBe(5);

    Livewire::test(NotificationList::class)
        ->call('markAllAsRead')
        ->assertDispatched('notification');

    expect($this->user->unreadNotifications()->count())->toBe(0);
});

it('deletes single notification', function () {
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

    Livewire::test(NotificationList::class)
        ->call('deleteNotification', $notificationId)
        ->assertDispatched('notification');

    expect($this->user->notifications()->where('id', $notificationId)->exists())->toBeFalse();
});

it('deletes all read notifications', function () {
    // Create 3 read notifications
    for ($i = 0; $i < 3; $i++) {
        $this->user->notify(new class extends \Illuminate\Notifications\Notification {
            public function via($notifiable) {
                return ['database'];
            }

            public function toArray($notifiable) {
                return ['message' => 'Read notification'];
            }
        });
    }
    $this->user->unreadNotifications->markAsRead();

    // Create 2 unread notifications
    for ($i = 0; $i < 2; $i++) {
        $this->user->notify(new class extends \Illuminate\Notifications\Notification {
            public function via($notifiable) {
                return ['database'];
            }

            public function toArray($notifiable) {
                return ['message' => 'Unread notification'];
            }
        });
    }

    expect($this->user->readNotifications()->count())->toBe(3);
    expect($this->user->unreadNotifications()->count())->toBe(2);

    Livewire::test(NotificationList::class)
        ->call('deleteAllRead')
        ->assertDispatched('notification');

    expect($this->user->readNotifications()->count())->toBe(0);
    expect($this->user->unreadNotifications()->count())->toBe(2);
});

it('paginates notifications', function () {
    // Create 25 notifications
    for ($i = 0; $i < 25; $i++) {
        $this->user->notify(new class extends \Illuminate\Notifications\Notification {
            public function via($notifiable) {
                return ['database'];
            }

            public function toArray($notifiable) {
                return ['message' => 'Test notification ' . uniqid()];
            }
        });
    }

    $component = Livewire::test(NotificationList::class);

    // Should show 20 per page
    expect($component->get('notifications')->count())->toBe(20);
});

it('displays notification details when available', function () {
    $this->user->notify(new class extends \Illuminate\Notifications\Notification {
        public function via($notifiable) {
            return ['database'];
        }

        public function toArray($notifiable) {
            return [
                'message' => 'Main message',
                'details' => 'Additional details here',
            ];
        }
    });

    Livewire::test(NotificationList::class)
        ->assertSee('Main message')
        ->assertSee('Additional details here');
});

it('displays action link when action_url is available', function () {
    $this->user->notify(new class extends \Illuminate\Notifications\Notification {
        public function via($notifiable) {
            return ['database'];
        }

        public function toArray($notifiable) {
            return [
                'message' => 'Test notification',
                'action_url' => '/test-action-url',
            ];
        }
    });

    Livewire::test(NotificationList::class)
        ->assertSee('View Details')
        ->assertSee('/test-action-url');
});
