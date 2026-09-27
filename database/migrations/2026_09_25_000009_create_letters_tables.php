<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('letter_types', function (Blueprint $table) {
            $table->id();
            $table->enum('scope_type', ['rw', 'rt']);
            $table->unsignedBigInteger('scope_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->string('template_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['scope_type', 'scope_id']);
        });

        Schema::create('letter_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_type_id')->constrained('letter_types')->onDelete('restrict');
            $table->foreignId('resident_id')->constrained('residents')->onDelete('restrict');
            $table->string('request_number')->nullable()->unique();
            $table->text('purpose');
            $table->json('form_data')->nullable();
            $table->string('status')->default('submitted'); // submitted, processing, approved, rejected, completed
            $table->timestamp('submitted_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('letter_workflows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_type_id')->constrained('letter_types')->onDelete('cascade');
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('letter_workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained('letter_workflows')->onDelete('cascade');
            $table->integer('step_order');
            $table->foreignId('role_id')->constrained('roles')->onDelete('restrict');
            $table->string('action_name');
            $table->boolean('is_required')->default(true);
            $table->timestamps();
        });

        Schema::create('letter_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_request_id')->constrained('letter_requests')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->integer('step_order');
            $table->string('decision'); // approved, rejected
            $table->text('notes')->nullable();
            $table->timestamp('acted_at');
            $table->timestamps();
        });

        Schema::create('letter_outputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_request_id')->constrained('letter_requests')->onDelete('cascade');
            $table->string('file_path');
            $table->string('document_number')->unique();
            $table->foreignId('generated_by')->constrained('users')->onDelete('restrict');
            $table->timestamp('generated_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_outputs');
        Schema::dropIfExists('letter_approvals');
        Schema::dropIfExists('letter_workflow_steps');
        Schema::dropIfExists('letter_workflows');
        Schema::dropIfExists('letter_requests');
        Schema::dropIfExists('letter_types');
    }
};
