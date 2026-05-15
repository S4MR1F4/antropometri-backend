<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Add composite indexes for dashboard, history, export, and duplicate checks.
     */
    public function up(): void
    {
        Schema::table('measurements', function (Blueprint $table) {
            $table->index(['measurement_date', 'deleted_at'], 'idx_measurements_date_deleted');
            $table->index(['user_id', 'measurement_date', 'deleted_at'], 'idx_measurements_user_date_deleted');
            $table->index(['category', 'measurement_date', 'deleted_at'], 'idx_measurements_category_date_deleted');
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->index(['normalized_name', 'date_of_birth'], 'idx_subjects_normalized_birth');
            $table->index(['deleted_at', 'created_at'], 'idx_subjects_deleted_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('measurements', function (Blueprint $table) {
            $table->dropIndex('idx_measurements_date_deleted');
            $table->dropIndex('idx_measurements_user_date_deleted');
            $table->dropIndex('idx_measurements_category_date_deleted');
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropIndex('idx_subjects_normalized_birth');
            $table->dropIndex('idx_subjects_deleted_created');
        });
    }
};
