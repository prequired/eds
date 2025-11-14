<?php

use App\Livewire\Tenant\Invoice\InvoiceList;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(InvoiceList::class)
        ->assertStatus(200);
});
