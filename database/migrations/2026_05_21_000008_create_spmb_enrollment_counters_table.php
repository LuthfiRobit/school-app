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
        Schema::create('spmb_enrollment_counters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spmb_track_id')->constrained('spmb_tracks')->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedInteger('counter')->default(0);
            $table->timestamps();

            // One counter row per track per year
            $table->unique(['spmb_track_id', 'year'], 'counter_track_year_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spmb_enrollment_counters');
    }
};
