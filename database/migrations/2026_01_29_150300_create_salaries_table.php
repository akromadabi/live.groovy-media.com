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
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->year('year');
            $table->tinyInteger('month'); // 1-12
            $table->tinyInteger('term'); // 1 = tanggal 1-15, 2 = tanggal 16-akhir bulan
            $table->date('period_start');
            $table->date('period_end');

            // Komponen gaji
            $table->decimal('total_hours', 8, 2)->default(0);
            $table->decimal('live_salary', 12, 2)->default(0); // Gaji dari durasi live
            $table->decimal('content_edit_bonus', 12, 2)->default(0); // Bonus konten edit
            $table->decimal('content_live_bonus', 12, 2)->default(0); // Bonus konten live
            $table->integer('total_sales')->default(0); // Total penjualan
            $table->decimal('sales_bonus', 12, 2)->default(0); // Bonus penjualan (dari tier)
            $table->boolean('target_met')->default(false); // Apakah target tercapai

            // Potongan
            $table->decimal('deduction', 12, 2)->default(0);
            $table->text('deduction_notes')->nullable();

            // Total
            $table->decimal('total_salary', 12, 2)->default(0);
            $table->enum('status', ['draft', 'finalized', 'paid'])->default('draft');
            $table->date('paid_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'year', 'month', 'term']);
            $table->index(['year', 'month', 'term']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
