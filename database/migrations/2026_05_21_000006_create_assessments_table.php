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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('applicant_enrollments')->cascadeOnDelete();
            $table->foreignId('spmb_track_assessment_id')->constrained('spmb_track_assessments')->cascadeOnDelete();
            $table->foreignId('assessed_by')->constrained('users')->cascadeOnDelete();
            $table->decimal('score', 5, 2)->nullable();
            $table->enum('grade', ['pass', 'fail'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('assessed_at');
            $table->timestamps();

            // Prevent duplicate assessments for the same assessment type on a single enrollment
            $table->unique(['enrollment_id', 'spmb_track_assessment_id'], 'enrollment_assessment_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
