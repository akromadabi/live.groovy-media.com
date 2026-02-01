<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tiktok_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('tiktok_reports', 'original_filename')) {
                $table->string('original_filename')->nullable()->after('filename');
            }
            if (!Schema::hasColumn('tiktok_reports', 'total_records')) {
                $table->integer('total_records')->default(0)->after('report_data');
            }
            if (!Schema::hasColumn('tiktok_reports', 'total_duration_minutes')) {
                $table->integer('total_duration_minutes')->default(0)->after('total_records');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tiktok_reports', function (Blueprint $table) {
            $table->dropColumn(['original_filename', 'total_records', 'total_duration_minutes']);
        });
    }
};
