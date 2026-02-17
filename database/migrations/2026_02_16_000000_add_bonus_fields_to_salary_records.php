<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('salary_records', function (Blueprint $table) {
            $table->decimal('base_salary', 15, 2)->default(0)->after('amount');
            $table->decimal('bonus_amount', 15, 2)->default(0)->after('base_salary');
            $table->boolean('target_met')->default(false)->after('bonus_amount');
        });
    }

    public function down(): void
    {
        Schema::table('salary_records', function (Blueprint $table) {
            $table->dropColumn(['base_salary', 'bonus_amount', 'target_met']);
        });
    }
};
