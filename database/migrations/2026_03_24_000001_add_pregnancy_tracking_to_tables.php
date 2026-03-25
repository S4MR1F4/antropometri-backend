<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $blueprint) {
            $blueprint->date('pregnancy_start_date')->nullable()->after('gender');
        });

        Schema::table('measurements', function (Blueprint $blueprint) {
            $blueprint->integer('gestational_age_weeks')->nullable()->after('is_pregnant');
            $blueprint->integer('trimester')->nullable()->after('gestational_age_weeks');
            $blueprint->decimal('pregnancy_weight_gain', 8, 2)->nullable()->after('trimester');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $blueprint) {
            $blueprint->dropColumn('pregnancy_start_date');
        });

        Schema::table('measurements', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['gestational_age_weeks', 'trimester', 'pregnancy_weight_gain']);
        });
    }
};
