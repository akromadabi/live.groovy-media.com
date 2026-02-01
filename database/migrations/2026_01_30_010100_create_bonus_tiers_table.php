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
        Schema::create('bonus_tiers', function (Blueprint $table) {
            $table->id();
            $table->integer('min_sales')->default(0); // Minimum penjualan untuk tier ini
            $table->integer('max_sales')->nullable(); // Maximum penjualan (null = unlimited)
            $table->decimal('bonus_amount', 12, 2)->default(0); // Jumlah bonus
            $table->string('description')->nullable(); // Deskripsi tier
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bonus_tiers');
    }
};
