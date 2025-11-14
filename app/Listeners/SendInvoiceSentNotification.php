<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\Tenant\InvoiceSent;
use App\Notifications\InvoiceSentNotification;

class SendInvoiceSentNotification
{
    public function handle(InvoiceSent $event): void
    {
        // Notify the client if they have an email
        if ($event->invoice->client && $event->invoice->client->email) {
            $event->invoice->client->notify(new InvoiceSentNotification($event->invoice));
        }

        // Notify the invoice creator/owner
        if ($event->invoice->user) {
            $event->invoice->user->notify(new InvoiceSentNotification($event->invoice));
        }
    }
}
