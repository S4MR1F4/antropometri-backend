<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Per 05_database_erd.md and 06_data_dictionary.md
     */
    public function up(): void
    {
        Schema::create('measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('measurement_date');
            $table->enum('category', ['balita', 'remaja', 'dewasa']);

            // Measurement data
            $table->decimal('weight', 5, 2);           // kg
            $table->decimal('height', 5, 2);           // cm
            $table->decimal('head_circumference', 5, 2)->nullable();   // cm, balita only
            $table->decimal('waist_circumference', 5, 2)->nullable();  // cm, dewasa only
            $table->enum('measurement_type', ['berbaring', 'berdiri'])->nullable(); // balita only

            // Age at measurement
            $table->unsignedInteger('age_in_months');
            $table->unsignedInteger('age_in_years');

            // Calculated values (stored, not computed at runtime)
            $table->decimal('bmi', 5, 2)->nullable();           // Dewasa only
            $table->decimal('zscore_bbu', 5, 2)->nullable();    // Balita BB/U
            $table->decimal('zscore_tbu', 5, 2)->nullable();    // Balita TB/U
            $table->decimal('zscore_bbtb', 5, 2)->nullable();   // Balita BB/TB
            $table->decimal('zscore_imtu', 5, 2)->nullable();   // Remaja IMT/U

            // Status labels
            $table->string('status_bbu', 50)->nullable();
            $table->string('status_tbu', 50)->nullable();
            $table->string('status_bbtb', 50)->nullable();
            $table->string('status_imtu', 50)->nullable();
            $table->string('status_bmi', 50)->nullable();
            $table->boolean('has_central_obesity')->nullable(); // Dewasa only

            // Notes
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Performance indexes per 05_database_erd.md §4.3
            $table->index(['subject_id', 'measurement_date'], 'idx_subject_date');
            $table->index(['user_id', 'category', 'deleted_at'], 'idx_user_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('measurements');
    }
};
