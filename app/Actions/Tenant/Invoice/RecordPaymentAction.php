<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Invoice;

use App\Data\Tenant\Invoice\RecordPaymentData;
use App\Enums\InvoiceStatus;
use App\Events\Tenant\InvoicePaymentRecorded;
use App\Models\Tenant\Invoice;
use App\Models\Tenant\Payment;
use Illuminate\Support\Facades\DB;

class RecordPaymentAction
{
    public function __invoke(Invoice $invoice, RecordPaymentData $data): Payment
    {
        if ($invoice->status === InvoiceStatus::CANCELLED) {
            throw new \Exception('Cannot record payment for a cancelled invoice.');
        }

        if ($data->amount <= 0) {
            throw new \Exception('Payment amount must be greater than zero.');
        }

        if ($data->amount > $invoice->remaining_amount) {
            throw new \Exception('Payment amount cannot exceed the remaining balance.');
        }

        return DB::transaction(function () use ($invoice, $data) {
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $data->amount,
                'payment_date' => $data->payment_date,
                'payment_method' => $data->payment_method,
                'transaction_id' => $data->transaction_id,
                'notes' => $data->notes,
            ]);

            // Update invoice
            $newAmountPaid = $invoice->amount_paid + $data->amount;
            $updates = [
                'amount_paid' => $newAmountPaid,
            ];

            // Update status based on payment
            if ($newAmountPaid >= $invoice->total) {
                $updates['status'] = InvoiceStatus::PAID;
                $updates['paid_date'] = $data->payment_date;
            } elseif ($newAmountPaid > 0) {
                $updates['status'] = InvoiceStatus::PARTIALLY_PAID;
            }

            $invoice->update($updates);

            InvoicePaymentRecorded::dispatch($invoice, $payment);

            return $payment;
        });
    }
}
