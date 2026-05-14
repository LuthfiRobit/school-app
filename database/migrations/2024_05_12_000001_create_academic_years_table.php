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
        // 1. Academic Years Table (Parent)
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Example: 2024/2025');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false);
            
            // Audit Columns
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 2. Semesters Table (Child)
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->enum('type', ['ganjil', 'genap', 'antara'])->default('ganjil');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false);
            
            // Audit Columns
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Constraint: Only one type per academic year
            $table->unique(['academic_year_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semesters');
        Schema::dropIfExists('academic_years');
    }
};
