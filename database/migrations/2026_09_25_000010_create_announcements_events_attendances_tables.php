<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->string('title');
            $table->text('body');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('status')->default('draft'); // draft, published, archived
            $table->timestamps();
        });

        Schema::create('announcement_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained('announcements')->onDelete('cascade');
            $table->string('target_type'); // rw, rt, all
            $table->unsignedBigInteger('target_id')->nullable();
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('rw_id')->nullable()->constrained('rws')->onDelete('cascade');
            $table->foreignId('rt_id')->nullable()->constrained('rts')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->boolean('registration_enabled')->default(true);
            $table->string('status')->default('upcoming'); // upcoming, ongoing, completed, cancelled
            $table->timestamps();
        });

        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('resident_id')->constrained('residents')->onDelete('cascade');
            $table->string('status')->default('registered'); // registered, attended, cancelled
            $table->timestamp('registered_at');
            $table->timestamps();

            $table->unique(['event_id', 'resident_id']);
        });

        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('token_hash', 64)->unique();
            $table->dateTime('valid_from');
            $table->dateTime('valid_until');
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->string('status')->default('active'); // active, closed
            $table->timestamps();
        });

        Schema::create('attendance_scan_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('attendance_sessions')->onDelete('cascade');
            $table->foreignId('resident_id')->nullable()->constrained('residents')->onDelete('set null');
            $table->foreignId('scanned_by')->constrained('users')->onDelete('restrict');
            $table->boolean('token_valid');
            $table->string('result'); // success, invalid_token, expired, duplicate
            $table->timestamp('scanned_at');
            $table->timestamps();
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('resident_id')->constrained('residents')->onDelete('cascade');
            $table->foreignId('session_id')->nullable()->constrained('attendance_sessions')->onDelete('set null');
            $table->string('attendance_method')->default('qr_scan'); // qr_scan, manual
            $table->foreignId('marked_by')->constrained('users')->onDelete('restrict');
            $table->string('status')->default('present'); // present, excused
            $table->timestamp('attended_at');
            $table->timestamps();

            $table->unique(['event_id', 'resident_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('attendance_scan_logs');
        Schema::dropIfExists('attendance_sessions');
        Schema::dropIfExists('event_registrations');
        Schema::dropIfExists('events');
        Schema::dropIfExists('announcement_targets');
        Schema::dropIfExists('announcements');
    }
};
