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
            $table->index('attendance_date');
            $table->index('status');
            $table->index(['attendance_date', 'status']);
        });

        Schema::table('salary_records', function (Blueprint $table) {
            $table->index(['year', 'month', 'term']);
            $table->index('status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['attendance_date']);
            $table->dropIndex(['status']);
            $table->dropIndex(['attendance_date', 'status']);
        });

        Schema::table('salary_records', function (Blueprint $table) {
            $table->dropIndex(['year', 'month', 'term']);
            $table->dropIndex(['status']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role', 'is_active']);
        });
    }
};
