<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Tenant\Expense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExpenseSubmittedNotification extends Notification implements ShouldQueue
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
            ->subject("New Expense Submitted for Approval")
            ->greeting("Hello!")
            ->line("{$this->expense->user->name} has submitted an expense for your approval.")
            ->line("**Category:** {$this->expense->category->label()}")
            ->line("**Amount:** \${$this->expense->amount}")
            ->line("**Date:** {$this->expense->expense_date->format('F j, Y')}")
            ->line("**Description:** {$this->expense->description}")
            ->action('Review Expense', $viewUrl)
            ->line('Please review and approve or reject this expense.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "{$this->expense->user->name} submitted an expense for approval",
            'details' => "{$this->expense->category->label()} - \${$this->expense->amount}",
            'action_url' => route('expenses.edit', $this->expense),
        ];
    }
}
