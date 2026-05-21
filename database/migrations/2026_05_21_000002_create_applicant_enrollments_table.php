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
        Schema::create('applicant_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained('applicants')->cascadeOnDelete();
            $table->foreignId('spmb_track_id')->constrained('spmb_tracks')->cascadeOnDelete();
            $table->string('enrollment_number', 30)->unique();
            $table->enum('status', [
                'draft', 
                'registered', 
                'waiting_payment_reg',
                'verified_reg', 
                'in_review', 
                'passed',
                'waiting_list', 
                'rejected',
                'waiting_payment_final', 
                'settled', 
                'permanent_student'
            ])->default('draft');
            $table->unsignedInteger('waitlist_order')->nullable();
            $table->dateTime('announcement_visible_at')->nullable();
            $table->timestamp('enrolled_at')->nullable();

            // Audit trails
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint to prevent multiple applications on the same track by the same applicant
            $table->unique(['applicant_id', 'spmb_track_id'], 'applicant_track_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicant_enrollments');
    }
};
