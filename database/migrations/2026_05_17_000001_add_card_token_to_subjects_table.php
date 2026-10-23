<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->string('card_token', 64)->nullable()->unique()->after('id');
        });

        // Populate existing subjects with a cryptographically secure UUID token
        $subjects = DB::table('subjects')->whereNull('card_token')->get(['id']);
        foreach ($subjects as $s) {
            DB::table('subjects')->where('id', $s->id)->update([
                'card_token' => (string) Str::uuid(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('card_token');
        });
    }
};
