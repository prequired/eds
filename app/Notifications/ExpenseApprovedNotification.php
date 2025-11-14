<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Tenant\Expense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExpenseApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Expense $expense
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $viewUrl = route('expenses.edit', $this->expense);

        return (new MailMessage)
            ->subject("Expense Approved")
            ->greeting("Hello {$this->expense->user->name}!")
            ->line("Good news! Your expense has been approved.")
            ->line("**Category:** {$this->expense->category->label()}")
            ->line("**Amount:** \${$this->expense->amount}")
            ->line("**Date:** {$this->expense->expense_date->format('F j, Y')}")
            ->line("**Approved by:** {$this->expense->approvedBy->name}")
            ->action('View Expense', $viewUrl)
            ->line('Your reimbursement will be processed according to company policy.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "Your expense of \${$this->expense->amount} has been approved",
            'details' => "{$this->expense->category->label()} | Approved by {$this->expense->approvedBy->name}",
            'action_url' => route('expenses.edit', $this->expense),
        ];
    }
}
