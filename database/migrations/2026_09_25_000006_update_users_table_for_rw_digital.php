<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('resident_id')->nullable()->constrained('residents')->onDelete('set null');
            $table->foreignId('rw_id')->nullable()->constrained('rws')->onDelete('set null');
            $table->foreignId('rt_id')->nullable()->constrained('rts')->onDelete('set null');
            $table->string('phone')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('last_login_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['resident_id']);
            $table->dropForeign(['rw_id']);
            $table->dropForeign(['rt_id']);
            $table->dropColumn(['resident_id', 'rw_id', 'rt_id', 'phone', 'status', 'last_login_at']);
        });
    }
};
