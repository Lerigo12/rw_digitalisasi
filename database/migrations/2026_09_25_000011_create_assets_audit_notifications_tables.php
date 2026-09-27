<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rw_id')->nullable()->constrained('rws')->onDelete('cascade');
            $table->foreignId('rt_id')->nullable()->constrained('rts')->onDelete('cascade');
            $table->string('asset_code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('quantity')->default(1);
            $table->string('condition')->default('good'); // good, damaged, lost
            $table->string('storage_location')->nullable();
            $table->string('status')->default('available'); // available, borrowed, maintenance
            $table->timestamps();
        });

        Schema::create('asset_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            $table->foreignId('borrower_resident_id')->nullable()->constrained('residents')->onDelete('set null');
            $table->string('borrower_name');
            $table->integer('quantity');
            $table->date('loan_date');
            $table->date('due_date');
            $table->string('status')->default('active'); // active, returned, overdue
            $table->foreignId('approved_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();
        });

        Schema::create('asset_loan_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_loan_id')->constrained('asset_loans')->onDelete('cascade');
            $table->integer('returned_quantity');
            $table->string('condition_after')->default('good');
            $table->foreignId('received_by')->constrained('users')->onDelete('restrict');
            $table->timestamp('returned_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action'); // create, update, delete, verify, approve, login
            $table->string('subject_type');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['subject_type', 'subject_id']);
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type');
            $table->string('title');
            $table->text('body');
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('asset_loan_returns');
        Schema::dropIfExists('asset_loans');
        Schema::dropIfExists('assets');
    }
};
