<?php

use App\Livewire\Tenant\Invoice\InvoiceView;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(InvoiceView::class)
        ->assertStatus(200);
});
