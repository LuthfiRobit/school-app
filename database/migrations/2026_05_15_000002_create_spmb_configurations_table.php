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
        Schema::create('spmb_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->unique()->constrained('academic_years')->cascadeOnDelete();
            $table->date('reg_start_date');
            $table->date('reg_end_date');
            $table->integer('total_quota')->unsigned();
            $table->enum('status', ['draft', 'active', 'closed', 'archived'])->default('draft');
            
            // Audit Columns
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spmb_configurations');
    }
};
