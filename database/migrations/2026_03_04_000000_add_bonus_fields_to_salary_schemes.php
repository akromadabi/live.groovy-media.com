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
        Schema::table('salary_schemes', function (Blueprint $table) {
            $table->decimal('daily_live_hours', 4, 1)->nullable()->after('monthly_target_hours');
            $table->integer('monthly_leave_days')->nullable()->after('daily_live_hours');
            $table->integer('bonus_pcs_threshold')->nullable()->after('monthly_leave_days');
            $table->decimal('bonus_amount', 12, 2)->nullable()->after('bonus_pcs_threshold');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_schemes', function (Blueprint $table) {
            $table->dropColumn(['daily_live_hours', 'monthly_leave_days', 'bonus_pcs_threshold', 'bonus_amount']);
        });
    }
};
