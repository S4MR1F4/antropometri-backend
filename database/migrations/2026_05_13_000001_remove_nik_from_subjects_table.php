<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Remove NIK column from subjects table.
     * NIK data has been backed up to backup_nik_data_20260513.csv
     * and full DB backup at backup_20260513.sql
     */
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('nik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->text('nik')->nullable()->after('normalized_name');
        });
    }
};
