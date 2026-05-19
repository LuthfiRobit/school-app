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
        Schema::create('spmb_track_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spmb_track_id')->constrained('spmb_tracks')->cascadeOnDelete();
            $table->foreignId('master_assessment_type_id')->constrained('master_assessment_types')->cascadeOnDelete();
            $table->decimal('weight', 5, 2)->default(100.00); // Percentage weight
            $table->decimal('passing_score', 8, 2)->nullable();
            $table->integer('display_order')->default(0);
            
            // Audit trails
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Prevent duplicate assessment types in the same track
            $table->unique(['spmb_track_id', 'master_assessment_type_id'], 'spmb_track_assessment_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spmb_track_assessments');
    }
};
