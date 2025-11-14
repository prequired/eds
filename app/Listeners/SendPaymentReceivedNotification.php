<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\Tenant\InvoicePaymentRecorded;
use App\Notifications\PaymentReceivedNotification;

class SendPaymentReceivedNotification
{
    public function handle(InvoicePaymentRecorded $event): void
    {
        // Notify the invoice creator/owner
        if ($event->invoice->user) {
            $event->invoice->user->notify(
                new PaymentReceivedNotification($event->invoice, $event->payment)
            );
        }

        // Notify admins and owners
        $admins = \App\Models\User::where('role', \App\Enums\TeamRole::ADMIN)
            ->orWhere('role', \App\Enums\TeamRole::OWNER)
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(
                new PaymentReceivedNotification($event->invoice, $event->payment)
            );
        }
    }
}
