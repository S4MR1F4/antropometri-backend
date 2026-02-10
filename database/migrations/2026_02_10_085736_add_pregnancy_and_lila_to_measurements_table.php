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
        Schema::table('measurements', function (Blueprint $table) {
            $table->boolean('is_pregnant')->default(false)->after('measurement_type');
            $table->decimal('arm_circumference', 5, 2)->nullable()->after('is_pregnant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('measurements', function (Blueprint $table) {
            $table->dropColumn(['is_pregnant', 'arm_circumference']);
        });
    }
};
