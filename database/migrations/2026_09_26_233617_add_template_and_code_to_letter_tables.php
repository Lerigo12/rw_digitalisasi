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
        if (Schema::hasTable('letter_types')) {
            Schema::table('letter_types', function (Blueprint $table) {
                if (! Schema::hasColumn('letter_types', 'code')) {
                    $table->string('code')->nullable()->after('name');
                }
                if (! Schema::hasColumn('letter_types', 'template_content')) {
                    $table->longText('template_content')->nullable()->after('requirements');
                }
            });
        }

        if (Schema::hasTable('letter_outputs')) {
            Schema::table('letter_outputs', function (Blueprint $table) {
                if (! Schema::hasColumn('letter_outputs', 'generated_body')) {
                    $table->longText('generated_body')->nullable()->after('file_path');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('letter_types')) {
            Schema::table('letter_types', function (Blueprint $table) {
                $columns = [];
                if (Schema::hasColumn('letter_types', 'code')) {
                    $columns[] = 'code';
                }
                if (Schema::hasColumn('letter_types', 'template_content')) {
                    $columns[] = 'template_content';
                }
                if (! empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }

        if (Schema::hasTable('letter_outputs')) {
            Schema::table('letter_outputs', function (Blueprint $table) {
                if (Schema::hasColumn('letter_outputs', 'generated_body')) {
                    $table->dropColumn('generated_body');
                }
            });
        }
    }
};
