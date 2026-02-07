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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('normalized_name');
            $table->string('nik', 16)->nullable();
            $table->date('date_of_birth');
            $table->enum('gender', ['L', 'P']);
            $table->text('address')->nullable();
            $table->string('parent_name')->nullable();
            $table->string('phone', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint for duplicate prevention per 05_database_erd.md §2.2
            $table->unique(['user_id', 'normalized_name', 'date_of_birth'], 'idx_duplicate_check');

            // Performance indexes per 05_database_erd.md §4.3
            $table->index(['user_id', 'deleted_at'], 'idx_user_subjects');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
