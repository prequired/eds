<?php

declare(strict_types=1);

use App\Actions\Tenant\Invoice\CancelInvoiceAction;
use App\Actions\Tenant\Invoice\CreateInvoiceAction;
use App\Actions\Tenant\Invoice\RecordPaymentAction;
use App\Actions\Tenant\Invoice\SendInvoiceAction;
use App\Actions\Tenant\Invoice\UpdateInvoiceAction;
use App\Data\Tenant\Invoice\CreateInvoiceData;
use App\Data\Tenant\Invoice\InvoiceItemData;
use App\Data\Tenant\Invoice\RecordPaymentData;
use App\Data\Tenant\Invoice\UpdateInvoiceData;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Events\Tenant\InvoicePaymentRecorded;
use App\Events\Tenant\InvoiceSent;
use App\Models\Tenant\Client;
use App\Models\Tenant\Invoice;
use App\Models\Tenant\InvoiceItem;
use Illuminate\Support\Facades\Event;

uses()->group('invoicing');

beforeEach(function () {
    // Initialize tenancy for testing
    $this->tenant = createTenant();
    tenancy()->initialize($this->tenant);

    // Create test client
    $this->client = Client::factory()->create();
});

afterEach(function () {
    tenancy()->end();
});

test('can create invoice with items', function () {
    $data = CreateInvoiceData::from([
        'client_id' => $this->client->id,
        'issue_date' => now(),
        'due_date' => now()->addDays(30),
        'tax_rate' => 10,
        'items' => [
            InvoiceItemData::from([
                'description' => 'Web Development',
                'quantity' => 10,
                'unit_price' => 100,
            ]),
            InvoiceItemData::from([
                'description' => 'Consulting',
                'quantity' => 5,
                'unit_price' => 150,
            ]),
        ],
    ]);

    $action = new CreateInvoiceAction();
    $invoice = $action($data);

    expect($invoice)->toBeInstanceOf(Invoice::class)
        ->and($invoice->client_id)->toBe($this->client->id)
        ->and($invoice->status)->toBe(InvoiceStatus::DRAFT)
        ->and($invoice->invoice_number)->not()->toBeNull()
        ->and($invoice->items)->toHaveCount(2)
        ->and($invoice->subtotal)->toBe('1750.00')
        ->and($invoice->tax_amount)->toBe('175.00')
        ->and($invoice->total)->toBe('1925.00');
});

test('invoice number is auto-generated with correct format', function () {
    $invoice1 = Invoice::factory()->for($this->client)->create();
    $invoice2 = Invoice::factory()->for($this->client)->create();

    $year = now()->year;
    $month = now()->format('m');

    expect($invoice1->invoice_number)->toMatch("/^INV-{$year}{$month}-\d{4}$/")
        ->and($invoice2->invoice_number)->toMatch("/^INV-{$year}{$month}-\d{4}$/")
        ->and($invoice1->invoice_number)->not()->toBe($invoice2->invoice_number);
});

test('invoice items calculate totals automatically', function () {
    $invoice = Invoice::factory()->for($this->client)->create();

    InvoiceItem::factory()->for($invoice)->create([
        'quantity' => 2,
        'unit_price' => 100,
    ]);

    InvoiceItem::factory()->for($invoice)->create([
        'quantity' => 3,
        'unit_price' => 50,
    ]);

    $invoice->refresh();

    expect($invoice->subtotal)->toBe('350.00')
        ->and($invoice->items)->toHaveCount(2);
});

test('can update invoice', function () {
    $invoice = Invoice::factory()->for($this->client)->create();
    InvoiceItem::factory()->for($invoice)->create();

    $newData = UpdateInvoiceData::from([
        'tax_rate' => 15,
        'notes' => 'Updated notes',
        'items' => [
            InvoiceItemData::from([
                'description' => 'Updated Service',
                'quantity' => 5,
                'unit_price' => 200,
            ]),
        ],
    ]);

    $action = new UpdateInvoiceAction();
    $updatedInvoice = $action($invoice, $newData);

    expect($updatedInvoice->tax_rate)->toBe('15.00')
        ->and($updatedInvoice->notes)->toBe('Updated notes')
        ->and($updatedInvoice->items)->toHaveCount(1)
        ->and($updatedInvoice->subtotal)->toBe('1000.00');
});

test('cannot update non-editable invoice', function () {
    $invoice = Invoice::factory()->for($this->client)->paid()->create();

    $data = UpdateInvoiceData::from(['notes' => 'Test']);
    $action = new UpdateInvoiceAction();

    expect(fn() => $action($invoice, $data))
        ->toThrow(\Exception::class, 'cannot be edited');
});

test('can send invoice', function () {
    Event::fake();

    $invoice = Invoice::factory()->for($this->client)->create();
    InvoiceItem::factory()->for($invoice)->create();

    $action = new SendInvoiceAction();
    $sentInvoice = $action($invoice);

    expect($sentInvoice->status)->toBe(InvoiceStatus::SENT);
    Event::assertDispatched(InvoiceSent::class);
});

test('cannot send invoice without items', function () {
    $invoice = Invoice::factory()->for($this->client)->create();

    $action = new SendInvoiceAction();

    expect(fn() => $action($invoice))
        ->toThrow(\Exception::class, 'no items');
});

test('can record payment', function () {
    Event::fake();

    $invoice = Invoice::factory()->for($this->client)->sent()->create([
        'total' => 1000,
        'amount_paid' => 0,
    ]);

    $paymentData = RecordPaymentData::from([
        'amount' => 500,
        'payment_date' => now(),
        'payment_method' => PaymentMethod::CREDIT_CARD,
        'transaction_id' => 'TXN-123',
    ]);

    $action = new RecordPaymentAction();
    $payment = $action($invoice, $paymentData);

    $invoice->refresh();

    expect($payment->amount)->toBe('500.00')
        ->and($payment->transaction_id)->toBe('TXN-123')
        ->and($invoice->amount_paid)->toBe('500.00')
        ->and($invoice->status)->toBe(InvoiceStatus::PARTIALLY_PAID);

    Event::assertDispatched(InvoicePaymentRecorded::class);
});

test('invoice becomes paid when full amount is paid', function () {
    $invoice = Invoice::factory()->for($this->client)->sent()->create([
        'total' => 1000,
        'amount_paid' => 0,
    ]);

    $paymentData = RecordPaymentData::from([
        'amount' => 1000,
        'payment_date' => now(),
        'payment_method' => PaymentMethod::BANK_TRANSFER,
    ]);

    $action = new RecordPaymentAction();
    $action($invoice, $paymentData);

    $invoice->refresh();

    expect($invoice->status)->toBe(InvoiceStatus::PAID)
        ->and($invoice->paid_date)->not()->toBeNull()
        ->and($invoice->isFullyPaid())->toBeTrue();
});

test('cannot record payment exceeding remaining balance', function () {
    $invoice = Invoice::factory()->for($this->client)->sent()->create([
        'total' => 1000,
        'amount_paid' => 0,
    ]);

    $paymentData = RecordPaymentData::from([
        'amount' => 1500,
        'payment_date' => now(),
        'payment_method' => PaymentMethod::CASH,
    ]);

    $action = new RecordPaymentAction();

    expect(fn() => $action($invoice, $paymentData))
        ->toThrow(\Exception::class, 'cannot exceed');
});

test('cannot record payment for cancelled invoice', function () {
    $invoice = Invoice::factory()->for($this->client)->cancelled()->create();

    $paymentData = RecordPaymentData::from([
        'amount' => 100,
        'payment_date' => now(),
        'payment_method' => PaymentMethod::CASH,
    ]);

    $action = new RecordPaymentAction();

    expect(fn() => $action($invoice, $paymentData))
        ->toThrow(\Exception::class, 'cancelled');
});

test('can cancel draft invoice', function () {
    $invoice = Invoice::factory()->for($this->client)->draft()->create();

    $action = new CancelInvoiceAction();
    $cancelledInvoice = $action($invoice);

    expect($cancelledInvoice->status)->toBe(InvoiceStatus::CANCELLED);
});

test('cannot cancel paid invoice', function () {
    $invoice = Invoice::factory()->for($this->client)->paid()->create();

    $action = new CancelInvoiceAction();

    expect(fn() => $action($invoice))
        ->toThrow(\Exception::class, 'Cannot cancel a paid invoice');
});

test('cannot cancel invoice with payments', function () {
    $invoice = Invoice::factory()->for($this->client)->partiallyPaid()->create([
        'total' => 1000,
        'amount_paid' => 500,
    ]);

    $action = new CancelInvoiceAction();

    expect(fn() => $action($invoice))
        ->toThrow(\Exception::class, 'with payments');
});

test('invoice scopes work correctly', function () {
    // Create various invoices
    $draft = Invoice::factory()->for($this->client)->draft()->create();
    $sent = Invoice::factory()->for($this->client)->sent()->create();
    $paid = Invoice::factory()->for($this->client)->paid()->create();
    $overdue = Invoice::factory()->for($this->client)->overdue()->create();

    // Test scopes
    expect(Invoice::draft()->count())->toBe(1)
        ->and(Invoice::sent()->count())->toBe(1)
        ->and(Invoice::paid()->count())->toBe(1)
        ->and(Invoice::overdue()->count())->toBe(1)
        ->and(Invoice::unpaid()->count())->toBe(2); // sent + overdue
});

test('invoice helper methods work correctly', function () {
    $paidInvoice = Invoice::factory()->for($this->client)->create([
        'total' => 1000,
        'amount_paid' => 1000,
    ]);

    $partialInvoice = Invoice::factory()->for($this->client)->create([
        'total' => 1000,
        'amount_paid' => 500,
    ]);

    $unpaidInvoice = Invoice::factory()->for($this->client)->create([
        'total' => 1000,
        'amount_paid' => 0,
    ]);

    expect($paidInvoice->isFullyPaid())->toBeTrue()
        ->and($paidInvoice->isPartiallyPaid())->toBeFalse()
        ->and($paidInvoice->remaining_amount)->toBe(0.0)
        ->and($partialInvoice->isFullyPaid())->toBeFalse()
        ->and($partialInvoice->isPartiallyPaid())->toBeTrue()
        ->and($partialInvoice->remaining_amount)->toBe(500.0)
        ->and($unpaidInvoice->isFullyPaid())->toBeFalse()
        ->and($unpaidInvoice->isPartiallyPaid())->toBeFalse()
        ->and($unpaidInvoice->remaining_amount)->toBe(1000.0);
});

test('overdue detection works correctly', function () {
    $overdueInvoice = Invoice::factory()->for($this->client)->create([
        'status' => InvoiceStatus::SENT,
        'due_date' => now()->subDays(1),
        'amount_paid' => 0,
    ]);

    $notOverdueInvoice = Invoice::factory()->for($this->client)->create([
        'status' => InvoiceStatus::SENT,
        'due_date' => now()->addDays(7),
        'amount_paid' => 0,
    ]);

    $paidInvoice = Invoice::factory()->for($this->client)->create([
        'status' => InvoiceStatus::PAID,
        'due_date' => now()->subDays(10),
        'amount_paid' => 1000,
        'total' => 1000,
    ]);

    expect($overdueInvoice->isOverdue())->toBeTrue()
        ->and($notOverdueInvoice->isOverdue())->toBeFalse()
        ->and($paidInvoice->isOverdue())->toBeFalse();
});

test('invoice status permissions work correctly', function () {
    $draft = Invoice::factory()->for($this->client)->draft()->create();
    $sent = Invoice::factory()->for($this->client)->sent()->create();
    $paid = Invoice::factory()->for($this->client)->paid()->create();

    expect($draft->canEdit())->toBeTrue()
        ->and($draft->canSend())->toBeTrue()
        ->and($sent->canEdit())->toBeTrue()
        ->and($sent->canSend())->toBeFalse()
        ->and($paid->canEdit())->toBeFalse()
        ->and($paid->canSend())->toBeFalse();
});

test('deleting invoice items recalculates totals', function () {
    $invoice = Invoice::factory()->for($this->client)->create(['tax_rate' => 10]);

    $item1 = InvoiceItem::factory()->for($invoice)->create([
        'quantity' => 2,
        'unit_price' => 100,
    ]);

    $item2 = InvoiceItem::factory()->for($invoice)->create([
        'quantity' => 3,
        'unit_price' => 50,
    ]);

    $invoice->refresh();
    expect($invoice->subtotal)->toBe('350.00');

    $item2->delete();

    $invoice->refresh();
    expect($invoice->subtotal)->toBe('200.00')
        ->and($invoice->tax_amount)->toBe('20.00')
        ->and($invoice->total)->toBe('220.00');
});

test('invoice relationships work correctly', function () {
    $invoice = Invoice::factory()->for($this->client)->create();
    InvoiceItem::factory()->count(3)->for($invoice)->create();

    expect($invoice->client)->toBeInstanceOf(Client::class)
        ->and($invoice->client->id)->toBe($this->client->id)
        ->and($invoice->items)->toHaveCount(3)
        ->and($invoice->items->first())->toBeInstanceOf(InvoiceItem::class);
});
