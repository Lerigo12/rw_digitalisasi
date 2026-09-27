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
        Schema::table('fee_types', function (Blueprint $table) {
            $table->json('allowed_payment_methods')->nullable()->after('due_day');
            $table->string('bank_name')->nullable()->after('allowed_payment_methods');
            $table->string('bank_account_number')->nullable()->after('bank_name');
            $table->string('bank_account_name')->nullable()->after('bank_account_number');
            $table->string('qris_image_path')->nullable()->after('bank_account_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_types', function (Blueprint $table) {
            $table->dropColumn([
                'allowed_payment_methods',
                'bank_name',
                'bank_account_number',
                'bank_account_name',
                'qris_image_path',
            ]);
        });
    }
};
