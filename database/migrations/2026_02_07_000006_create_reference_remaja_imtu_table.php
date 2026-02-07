<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Reference table: IMT per Umur (IMT/U) untuk Remaja
     * Per 05_database_erd.md §2.4 and 06_data_dictionary.md §5.4
     * 
     * Source: WHO/Kemenkes standards for adolescents 5-18 years (61-216 months)
     */
    public function up(): void
    {
        Schema::create('reference_remaja_imtu', function (Blueprint $table) {
            $table->id();
            $table->enum('gender', ['L', 'P']);
            $table->unsignedInteger('age_months');  // 61-216 (5-18 years)
            $table->decimal('neg3sd', 6, 3);        // -3 SD
            $table->decimal('neg2sd', 6, 3);        // -2 SD
            $table->decimal('neg1sd', 6, 3);        // -1 SD
            $table->decimal('median', 6, 3);        // 0 SD (median)
            $table->decimal('pos1sd', 6, 3);        // +1 SD
            $table->decimal('pos2sd', 6, 3);        // +2 SD
            $table->decimal('pos3sd', 6, 3);        // +3 SD

            // Unique index for lookups
            $table->unique(['gender', 'age_months'], 'idx_gender_age');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reference_remaja_imtu');
    }
};
