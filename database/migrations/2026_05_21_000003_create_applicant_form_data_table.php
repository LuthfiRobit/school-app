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
        Schema::create('applicant_form_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('applicant_enrollments')->cascadeOnDelete();
            $table->foreignId('field_id')->constrained('spmb_track_form_fields')->cascadeOnDelete();
            $table->text('value')->nullable();
            $table->boolean('is_valid')->nullable();
            $table->text('validation_note')->nullable();
            $table->timestamps();

            // Prevent duplicate answers for the same field in a single enrollment
            $table->unique(['enrollment_id', 'field_id'], 'form_data_field_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicant_form_data');
    }
};
