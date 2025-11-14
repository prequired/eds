<?php

use App\Livewire\Tenant\Invoice\InvoiceForm;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(InvoiceForm::class)
        ->assertStatus(200);
});
