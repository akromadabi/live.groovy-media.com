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
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('platform', 30)->default('tiktok')->after('session_name');
            $table->index('platform');
            $table->index(['user_id', 'attendance_date', 'platform']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'attendance_date', 'platform']);
            $table->dropIndex(['platform']);
            $table->dropColumn('platform');
        });
    }
};
