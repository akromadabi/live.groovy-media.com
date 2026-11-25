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
        Schema::table('attendances', function (Blueprint $table) {
            $table->index(['user_id', 'attendance_date'], 'idx_att_user_date');
            $table->index(['user_id', 'status', 'attendance_date'], 'idx_att_user_status_date');
            $table->index(['platform', 'attendance_date'], 'idx_att_platform_date');
        });

        Schema::table('tiktok_report_details', function (Blueprint $table) {
            $table->index(['user_id', 'live_date'], 'idx_trd_user_date');
            $table->index('live_date', 'idx_trd_live_date');
        });

        Schema::table('salaries', function (Blueprint $table) {
            $table->index(['user_id', 'period_end'], 'idx_sal_user_period_end');
            $table->index('period_start', 'idx_sal_period_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex('idx_att_user_date');
            $table->dropIndex('idx_att_user_status_date');
            $table->dropIndex('idx_att_platform_date');
        });

        Schema::table('tiktok_report_details', function (Blueprint $table) {
            $table->dropIndex('idx_trd_user_date');
            $table->dropIndex('idx_trd_live_date');
        });

        Schema::table('salaries', function (Blueprint $table) {
            $table->dropIndex('idx_sal_user_period_end');
            $table->dropIndex('idx_sal_period_start');
        });
    }
};
