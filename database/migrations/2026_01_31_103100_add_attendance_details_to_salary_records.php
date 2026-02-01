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
        Schema::table('salary_records', function (Blueprint $table) {
            $table->decimal('total_hours', 10, 2)->default(0)->after('amount');
            $table->integer('total_live_count')->default(0)->after('total_hours');
            $table->integer('total_sales')->default(0)->after('total_live_count');
            $table->integer('total_content_edit')->default(0)->after('total_sales');
            $table->integer('total_content_live')->default(0)->after('total_content_edit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_records', function (Blueprint $table) {
            $table->dropColumn([
                'total_hours',
                'total_live_count',
                'total_sales',
                'total_content_edit',
                'total_content_live',
            ]);
        });
    }
};
