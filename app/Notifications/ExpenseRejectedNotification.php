<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Tenant\Expense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExpenseRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Expense $expense,
        public ?string $rejectionReason = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $viewUrl = route('expenses.edit', $this->expense);

        $message = (new MailMessage)
            ->subject("Expense Rejected")
            ->greeting("Hello {$this->expense->user->name}!")
            ->line("Your expense has been rejected.")
            ->line("**Category:** {$this->expense->category->label()}")
            ->line("**Amount:** \${$this->expense->amount}")
            ->line("**Date:** {$this->expense->expense_date->format('F j, Y')}");

        if ($this->rejectionReason) {
            $message->line("**Reason:** {$this->rejectionReason}");
        }

        return $message
            ->action('View Expense', $viewUrl)
            ->line('Please contact your manager if you have questions about this decision.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "Your expense of \${$this->expense->amount} has been rejected",
            'details' => $this->rejectionReason
                ? "Reason: {$this->rejectionReason}"
                : "{$this->expense->category->label()} | {$this->expense->expense_date->format('M j, Y')}",
            'action_url' => route('expenses.edit', $this->expense),
        ];
    }
}
