<?php

use App\Enums\ExpenseCategory;
use App\Enums\ExpenseStatus;
use App\Livewire\Tenant\Expense\ExpenseList;
use App\Models\Tenant\Client;
use App\Models\Tenant\Expense;
use App\Models\Tenant\Project;
use App\Models\Tenant\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('renders successfully', function () {
    Livewire::test(ExpenseList::class)
        ->assertStatus(200);
});

it('displays expenses for authenticated user', function () {
    $expense = Expense::factory()->create([
        'user_id' => $this->user->id,
        'description' => 'Test Expense',
    ]);

    Livewire::test(ExpenseList::class)
        ->assertSee('Test Expense');
});

it('filters expenses by search query', function () {
    Expense::factory()->create([
        'user_id' => $this->user->id,
        'description' => 'Software subscription',
    ]);

    Expense::factory()->create([
        'user_id' => $this->user->id,
        'description' => 'Office supplies',
    ]);

    Livewire::test(ExpenseList::class)
        ->set('search', 'software')
        ->assertSee('Software subscription')
        ->assertDontSee('Office supplies');
});

it('filters expenses by category', function () {
    Expense::factory()->create([
        'user_id' => $this->user->id,
        'category' => ExpenseCategory::SOFTWARE,
        'description' => 'Software expense',
    ]);

    Expense::factory()->create([
        'user_id' => $this->user->id,
        'category' => ExpenseCategory::TRAVEL,
        'description' => 'Travel expense',
    ]);

    Livewire::test(ExpenseList::class)
        ->set('categoryFilter', ExpenseCategory::SOFTWARE->value)
        ->assertSee('Software expense')
        ->assertDontSee('Travel expense');
});

it('filters expenses by status', function () {
    Expense::factory()->draft()->create([
        'user_id' => $this->user->id,
        'description' => 'Draft expense',
    ]);

    Expense::factory()->approved()->create([
        'user_id' => $this->user->id,
        'description' => 'Approved expense',
    ]);

    Livewire::test(ExpenseList::class)
        ->set('statusFilter', ExpenseStatus::DRAFT->value)
        ->assertSee('Draft expense')
        ->assertDontSee('Approved expense');
});

it('filters expenses by project', function () {
    $project1 = Project::factory()->create(['name' => 'Project A']);
    $project2 = Project::factory()->create(['name' => 'Project B']);

    Expense::factory()->forProject($project1)->create([
        'user_id' => $this->user->id,
        'description' => 'Project A expense',
    ]);

    Expense::factory()->forProject($project2)->create([
        'user_id' => $this->user->id,
        'description' => 'Project B expense',
    ]);

    Livewire::test(ExpenseList::class)
        ->set('projectFilter', $project1->id)
        ->assertSee('Project A expense')
        ->assertDontSee('Project B expense');
});

it('filters expenses by client', function () {
    $client1 = Client::factory()->create(['name' => 'Client A']);
    $client2 = Client::factory()->create(['name' => 'Client B']);

    Expense::factory()->forClient($client1)->create([
        'user_id' => $this->user->id,
        'description' => 'Client A expense',
    ]);

    Expense::factory()->forClient($client2)->create([
        'user_id' => $this->user->id,
        'description' => 'Client B expense',
    ]);

    Livewire::test(ExpenseList::class)
        ->set('clientFilter', $client1->id)
        ->assertSee('Client A expense')
        ->assertDontSee('Client B expense');
});

it('filters expenses by billable status', function () {
    Expense::factory()->billable()->create([
        'user_id' => $this->user->id,
        'description' => 'Billable expense',
    ]);

    Expense::factory()->nonBillable()->create([
        'user_id' => $this->user->id,
        'description' => 'Non-billable expense',
    ]);

    Livewire::test(ExpenseList::class)
        ->set('billableFilter', 'yes')
        ->assertSee('Billable expense')
        ->assertDontSee('Non-billable expense');
});

it('calculates total amount correctly', function () {
    Expense::factory()->amount(100)->create(['user_id' => $this->user->id]);
    Expense::factory()->amount(250)->create(['user_id' => $this->user->id]);

    Livewire::test(ExpenseList::class)
        ->assertSet('totalAmount', 350.0);
});

it('calculates billable amount correctly', function () {
    Expense::factory()->billable()->amount(100)->create(['user_id' => $this->user->id]);
    Expense::factory()->billable()->amount(200)->create(['user_id' => $this->user->id]);
    Expense::factory()->nonBillable()->amount(150)->create(['user_id' => $this->user->id]);

    Livewire::test(ExpenseList::class)
        ->assertSet('billableAmount', 300.0);
});

it('allows user to submit their own draft expense', function () {
    $expense = Expense::factory()->draft()->create([
        'user_id' => $this->user->id,
        'description' => 'My Expense',
    ]);

    Livewire::test(ExpenseList::class)
        ->call('submitExpense', $expense->id)
        ->assertDispatched('notification');

    $expense->refresh();
    expect($expense->status)->toBe(ExpenseStatus::SUBMITTED);
});

it('prevents user from submitting another users expense', function () {
    $otherUser = User::factory()->create();
    $expense = Expense::factory()->draft()->create([
        'user_id' => $otherUser->id,
    ]);

    Livewire::test(ExpenseList::class)
        ->call('submitExpense', $expense->id)
        ->assertDispatched('notification');

    $expense->refresh();
    expect($expense->status)->toBe(ExpenseStatus::DRAFT);
});

it('shows only user expenses for non-admin users', function () {
    $otherUser = User::factory()->create();

    Expense::factory()->create([
        'user_id' => $this->user->id,
        'description' => 'My Expense',
    ]);

    Expense::factory()->create([
        'user_id' => $otherUser->id,
        'description' => 'Other User Expense',
    ]);

    Livewire::test(ExpenseList::class)
        ->assertSee('My Expense')
        ->assertDontSee('Other User Expense');
});

it('resets page when search is updated', function () {
    Livewire::test(ExpenseList::class)
        ->set('search', 'test')
        ->assertMethodWasCalled('resetPage');
});
