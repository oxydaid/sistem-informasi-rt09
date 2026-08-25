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
        Schema::create('cash_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('name');
            $table->string('account_number')->nullable();
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('cash_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('name');
            $table->string('type'); // 'income' or 'expense'
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['organization_id', 'type']);
        });

        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_account_id')->constrained('cash_accounts')->cascadeOnDelete();
            $table->foreignId('cash_category_id')->constrained('cash_categories')->cascadeOnDelete();
            $table->string('type'); // 'income' or 'expense'
            $table->decimal('amount', 15, 2);
            $table->date('transaction_date');
            $table->text('description');
            $table->string('reference_number')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('status')->default('confirmed'); // 'confirmed' or 'void'
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['cash_account_id', 'transaction_date']);
            $table->index(['cash_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_transactions');
        Schema::dropIfExists('cash_categories');
        Schema::dropIfExists('cash_accounts');
    }
};
