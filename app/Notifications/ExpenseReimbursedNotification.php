<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Tenant\Expense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExpenseReimbursedNotification extends Notification implements ShouldQueue
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
            ->subject("Expense Reimbursed")
            ->greeting("Hello {$this->expense->user->name}!")
            ->line("Your expense has been reimbursed.")
            ->line("**Category:** {$this->expense->category->label()}")
            ->line("**Amount:** \${$this->expense->amount}")
            ->line("**Date:** {$this->expense->expense_date->format('F j, Y')}")
            ->line("**Reimbursed on:** {$this->expense->reimbursed_at->format('F j, Y')}")
            ->action('View Expense', $viewUrl)
            ->line('The reimbursement has been processed and should appear in your account soon.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "Your expense of \${$this->expense->amount} has been reimbursed",
            'details' => "{$this->expense->category->label()} | {$this->expense->reimbursed_at->format('M j, Y')}",
            'action_url' => route('expenses.edit', $this->expense),
        ];
    }
}
