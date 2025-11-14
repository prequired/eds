<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Tenant\Invoice;
use App\Models\Tenant\InvoicePayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Invoice $invoice,
        public InvoicePayment $payment
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $viewUrl = route('invoices.view', $this->invoice);
        $isPaidInFull = $this->invoice->balance_due == 0;

        $message = (new MailMessage)
            ->subject("Payment Received - Invoice #{$this->invoice->invoice_number}")
            ->greeting("Hello!")
            ->line("We have received a payment for Invoice #{$this->invoice->invoice_number}.")
            ->line("**Payment Amount:** \${$this->payment->amount}")
            ->line("**Payment Date:** {$this->payment->payment_date->format('F j, Y')}")
            ->line("**Payment Method:** {$this->payment->payment_method}");

        if ($isPaidInFull) {
            $message->line("✅ **This invoice has been paid in full.**");
        } else {
            $message->line("**Remaining Balance:** \${$this->invoice->balance_due}");
        }

        return $message
            ->action('View Invoice', $viewUrl)
            ->line('Thank you for your payment!');
    }

    public function toArray(object $notifiable): array
    {
        $isPaidInFull = $this->invoice->balance_due == 0;

        return [
            'message' => "Payment of \${$this->payment->amount} received for Invoice #{$this->invoice->invoice_number}",
            'details' => $isPaidInFull
                ? 'Invoice has been paid in full'
                : "Remaining balance: \${$this->invoice->balance_due}",
            'action_url' => route('invoices.view', $this->invoice),
        ];
    }
}
