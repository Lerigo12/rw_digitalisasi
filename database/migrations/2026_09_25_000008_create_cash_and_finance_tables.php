<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rw_id')->nullable()->constrained('rws')->onDelete('cascade');
            $table->foreignId('rt_id')->nullable()->constrained('rts')->onDelete('cascade');
            $table->string('name');
            $table->string('account_type')->default('bank'); // bank, cash
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('cash_categories', function (Blueprint $table) {
            $table->id();
            $table->enum('scope_type', ['rw', 'rt']);
            $table->unsignedBigInteger('scope_id');
            $table->string('name');
            $table->string('transaction_type'); // income, expense
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['scope_type', 'scope_id']);
        });

        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_account_id')->constrained('cash_accounts')->onDelete('restrict');
            $table->foreignId('category_id')->constrained('cash_categories')->onDelete('restrict');
            $table->string('transaction_type'); // income, expense
            $table->decimal('amount', 15, 2);
            $table->date('transaction_date');
            $table->text('description');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('cash_transaction_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_transaction_id')->constrained('cash_transactions')->onDelete('cascade');
            $table->foreignId('approver_id')->constrained('users')->onDelete('restrict');
            $table->string('decision'); // approved, rejected
            $table->text('notes')->nullable();
            $table->timestamp('acted_at');
            $table->timestamps();
        });

        Schema::create('cash_transaction_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_transaction_id')->constrained('cash_transactions')->onDelete('cascade');
            $table->string('file_path');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('restrict');
            $table->timestamp('uploaded_at');
            $table->timestamps();
        });

        Schema::create('cash_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_account_id')->constrained('cash_accounts')->onDelete('restrict');
            $table->foreignId('destination_account_id')->constrained('cash_accounts')->onDelete('restrict');
            $table->decimal('amount', 15, 2);
            $table->date('transfer_date');
            $table->text('description');
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->string('status')->default('approved'); // approved, pending
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_transfers');
        Schema::dropIfExists('cash_transaction_proofs');
        Schema::dropIfExists('cash_transaction_approvals');
        Schema::dropIfExists('cash_transactions');
        Schema::dropIfExists('cash_categories');
        Schema::dropIfExists('cash_accounts');
    }
};
