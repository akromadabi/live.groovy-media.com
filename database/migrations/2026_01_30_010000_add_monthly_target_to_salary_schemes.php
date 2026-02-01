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
            $table->decimal('monthly_target_hours', 5, 1)->default(60)->after('content_live_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_schemes', function (Blueprint $table) {
            $table->dropColumn('monthly_target_hours');
        });
    }
};
