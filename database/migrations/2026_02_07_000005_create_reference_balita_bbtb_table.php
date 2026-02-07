<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Reference table: Berat Badan per Tinggi Badan (BB/TB) untuk Balita
     * Per 05_database_erd.md §2.4 and 06_data_dictionary.md §5.3
     * 
     * Source: WHO/Kemenkes standards for children 0-60 months
     * NOTE: This table uses height instead of age for lookup
     */
    public function up(): void
    {
        Schema::create('reference_balita_bbtb', function (Blueprint $table) {
            $table->id();
            $table->enum('gender', ['L', 'P']);
            $table->decimal('height', 5, 1);        // Height in cm
            $table->decimal('neg3sd', 6, 3);        // -3 SD
            $table->decimal('neg2sd', 6, 3);        // -2 SD
            $table->decimal('neg1sd', 6, 3);        // -1 SD
            $table->decimal('median', 6, 3);        // 0 SD (median)
            $table->decimal('pos1sd', 6, 3);        // +1 SD
            $table->decimal('pos2sd', 6, 3);        // +2 SD
            $table->decimal('pos3sd', 6, 3);        // +3 SD

            // Unique index for lookups
            $table->unique(['gender', 'height'], 'idx_gender_height');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reference_balita_bbtb');
    }
};
