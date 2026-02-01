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
        Schema::create('tiktok_report_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tiktok_report_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->date('live_date');
            $table->integer('duration_minutes')->default(0);
            $table->enum('match_status', ['matched', 'unmatched', 'needs_verification'])->default('needs_verification');
            $table->foreignId('matched_attendance_id')->nullable()->constrained('attendances')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tiktok_report_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiktok_report_details');
    }
};
