<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Tenant\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceSentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Invoice $invoice
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $viewUrl = route('invoices.view', $this->invoice);

        return (new MailMessage)
            ->subject("Invoice #{$this->invoice->invoice_number} from " . tenancy()->tenant->company_name)
            ->greeting("Hello {$this->invoice->client->name}!")
            ->line("You have received a new invoice from **" . tenancy()->tenant->company_name . "**.")
            ->line("**Invoice Number:** {$this->invoice->invoice_number}")
            ->line("**Amount:** \${$this->invoice->total_amount}")
            ->line("**Due Date:** {$this->invoice->due_date->format('F j, Y')}")
            ->action('View Invoice', $viewUrl)
            ->line('Please pay by the due date to avoid any late fees.')
            ->line('Thank you for your business!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "Invoice #{$this->invoice->invoice_number} has been sent to {$this->invoice->client->name}",
            'details' => "Amount: \${$this->invoice->total_amount} | Due: {$this->invoice->due_date->format('M j, Y')}",
            'action_url' => route('invoices.view', $this->invoice),
        ];
    }
}
