<?php

declare(strict_types=1);

namespace App\Models\Tenant;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $connection = 'tenant';

    protected $fillable = [
        'client_id',
        'invoice_number',
        'status',
        'issue_date',
        'due_date',
        'paid_date',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'total',
        'amount_paid',
        'notes',
        'terms',
        'footer',
    ];

    protected $casts = [
        'status' => InvoiceStatus::class,
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = static::generateInvoiceNumber();
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum('total');
        $this->tax_amount = $this->subtotal * ($this->tax_rate / 100);
        $this->total = $this->subtotal + $this->tax_amount;
    }

    public function getRemainingAmountAttribute(): float
    {
        return (float) ($this->total - $this->amount_paid);
    }

    public function isFullyPaid(): bool
    {
        return $this->amount_paid >= $this->total && $this->total > 0;
    }

    public function isPartiallyPaid(): bool
    {
        return $this->amount_paid > 0 && $this->amount_paid < $this->total;
    }

    public function isOverdue(): bool
    {
        return $this->due_date->isPast()
            && !$this->isFullyPaid()
            && $this->status !== InvoiceStatus::CANCELLED;
    }

    public function canEdit(): bool
    {
        return $this->status->canEdit();
    }

    public function canSend(): bool
    {
        return $this->status->canSend();
    }

    public function scopeDraft($query)
    {
        return $query->where('status', InvoiceStatus::DRAFT);
    }

    public function scopeSent($query)
    {
        return $query->where('status', InvoiceStatus::SENT);
    }

    public function scopePaid($query)
    {
        return $query->where('status', InvoiceStatus::PAID);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->whereNotIn('status', [InvoiceStatus::PAID, InvoiceStatus::CANCELLED])
            ->where(function ($q) {
                $q->whereColumn('amount_paid', '<', 'total')
                    ->orWhereNull('amount_paid')
                    ->orWhere('amount_paid', 0);
            });
    }

    public function scopeUnpaid($query)
    {
        return $query->whereNotIn('status', [InvoiceStatus::PAID, InvoiceStatus::CANCELLED])
            ->where(function ($q) {
                $q->whereColumn('amount_paid', '<', 'total')
                    ->orWhereNull('amount_paid')
                    ->orWhere('amount_paid', 0);
            });
    }

    protected static function generateInvoiceNumber(): string
    {
        $year = now()->year;
        $month = now()->format('m');

        // Get the last invoice number for this month
        $lastInvoice = static::whereYear('created_at', $year)
            ->whereMonth('created_at', now()->month)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastInvoice && preg_match('/INV-' . $year . $month . '-(\d+)/', $lastInvoice->invoice_number, $matches)) {
            $sequence = intval($matches[1]) + 1;
        } else {
            $sequence = 1;
        }

        return sprintf('INV-%s%s-%04d', $year, $month, $sequence);
    }
}
