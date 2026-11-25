<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('salary_schemes', function (Blueprint $table) {
            $table->decimal('hourly_rate', 12, 2)->nullable()->default(null)->change();
            $table->decimal('content_edit_rate', 12, 2)->nullable()->default(null)->change();
            $table->decimal('content_live_rate', 12, 2)->nullable()->default(null)->change();
            $table->decimal('monthly_target_hours', 5, 1)->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('salary_schemes', function (Blueprint $table) {
            $table->decimal('hourly_rate', 12, 2)->default(0)->change();
            $table->decimal('content_edit_rate', 12, 2)->default(0)->change();
            $table->decimal('content_live_rate', 12, 2)->default(0)->change();
            $table->decimal('monthly_target_hours', 5, 1)->default(60)->change();
        });
    }
};
