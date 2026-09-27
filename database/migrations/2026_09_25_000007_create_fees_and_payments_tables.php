<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_types', function (Blueprint $table) {
            $table->id();
            $table->enum('scope_type', ['rw', 'rt']);
            $table->unsignedBigInteger('scope_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('period_type')->default('monthly');
            $table->integer('due_day')->default(10);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['scope_type', 'scope_id']);
        });

        Schema::create('fee_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_type_id')->constrained('fee_types')->onDelete('cascade');
            $table->foreignId('resident_id')->constrained('residents')->onDelete('cascade');
            $table->string('period_key'); // e.g. "2026-09"
            $table->decimal('amount_due', 12, 2);
            $table->date('due_date');
            $table->string('status')->default('unpaid'); // unpaid, paid
            $table->timestamp('settled_at')->nullable();
            $table->timestamps();

            $table->unique(['fee_type_id', 'resident_id', 'period_key']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('payer_resident_id')->constrained('residents')->onDelete('restrict');
            $table->dateTime('payment_date');
            $table->decimal('total_amount', 12, 2);
            $table->string('method')->default('manual_transfer');
            $table->string('reference_number')->nullable();
            $table->string('status')->default('pending'); // pending, verified, rejected
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->foreignId('fee_bill_id')->constrained('fee_bills')->onDelete('cascade');
            $table->decimal('allocated_amount', 12, 2);
            $table->timestamps();
        });

        Schema::create('payment_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->string('file_path');
            $table->string('original_filename');
            $table->string('mime_type');
            $table->unsignedInteger('file_size');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('restrict');
            $table->timestamp('uploaded_at');
            $table->timestamps();
        });

        Schema::create('payment_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->foreignId('verified_by')->constrained('users')->onDelete('restrict');
            $table->string('decision'); // verified, rejected
            $table->text('notes')->nullable();
            $table->timestamp('verified_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_verifications');
        Schema::dropIfExists('payment_proofs');
        Schema::dropIfExists('payment_allocations');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('fee_bills');
        Schema::dropIfExists('fee_types');
    }
};
