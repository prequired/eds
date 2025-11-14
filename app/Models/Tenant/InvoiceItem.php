<?php

declare(strict_types=1);

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasFactory, HasUuids;

    protected $connection = 'tenant';

    protected $fillable = [
        'invoice_id',
        'description',
        'quantity',
        'unit_price',
        'total',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (InvoiceItem $item) {
            $item->total = $item->quantity * $item->unit_price;
        });

        static::saved(function (InvoiceItem $item) {
            $item->invoice->calculateTotals();
            $item->invoice->save();
        });

        static::deleted(function (InvoiceItem $item) {
            $item->invoice->calculateTotals();
            $item->invoice->save();
        });
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
