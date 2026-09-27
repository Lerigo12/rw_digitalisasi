<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->constrained('families')->onDelete('restrict');
            $table->string('nik_hash', 64)->unique();
            $table->text('nik_encrypted');
            $table->string('full_name');
            $table->string('birth_place');
            $table->date('birth_date');
            $table->enum('gender', ['L', 'P']);
            $table->string('religion')->nullable();
            $table->string('occupation')->nullable();
            $table->string('phone')->nullable();
            $table->string('status')->default('active');
            $table->string('verification_status')->default('pending');
            $table->timestamps();
        });

        Schema::table('families', function (Blueprint $table) {
            $table->foreign('head_resident_id')->references('id')->on('residents')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('families', function (Blueprint $table) {
            $table->dropForeign(['head_resident_id']);
        });
        Schema::dropIfExists('residents');
    }
};
