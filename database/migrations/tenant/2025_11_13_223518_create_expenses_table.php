<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('project_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->foreignUuid('client_id')->nullable()->constrained('clients')->onDelete('set null');
            $table->foreignUuid('invoice_id')->nullable()->constrained('invoices')->onDelete('set null');
            $table->string('category'); // ExpenseCategory enum
            $table->decimal('amount', 10, 2);
            $table->date('expense_date');
            $table->text('description');
            $table->string('receipt_path')->nullable();
            $table->boolean('billable')->default(false);
            $table->string('status')->default('draft'); // ExpenseStatus enum
            $table->text('notes')->nullable();
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('reimbursed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('user_id');
            $table->index('project_id');
            $table->index('client_id');
            $table->index('invoice_id');
            $table->index('category');
            $table->index('status');
            $table->index('expense_date');
            $table->index('billable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
