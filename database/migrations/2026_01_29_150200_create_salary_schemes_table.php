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
        Schema::create('salary_schemes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('hourly_rate', 12, 2)->default(0); // Gaji per jam live
            $table->decimal('content_edit_rate', 12, 2)->default(0); // Gaji per konten edit
            $table->decimal('content_live_rate', 12, 2)->default(0); // Gaji per konten live
            $table->decimal('sales_bonus_percentage', 5, 2)->default(0); // Bonus penjualan dalam %
            $table->decimal('sales_bonus_nominal', 12, 2)->default(0); // Bonus per penjualan (nominal)
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_schemes');
    }
};
