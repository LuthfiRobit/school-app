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
        Schema::create('spmb_track_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spmb_track_id')->constrained('spmb_tracks')->cascadeOnDelete();
            $table->string('field_group')->default('data_pribadi'); // e.g., "data_pribadi", "alamat", "kontak", "orang_tua"
            $table->string('field_name'); // e.g., "asal_sekolah", "nilai_un"
            $table->string('field_label'); // e.g., "Asal Sekolah", "Nilai UN"
            $table->enum('field_type', ['text', 'number', 'date', 'select', 'file', 'textarea', 'radio', 'checkbox'])->default('text');
            $table->json('field_options')->nullable(); // For dropdowns, e.g., ["SMA", "SMK"]
            $table->string('file_types')->nullable(); // For files, e.g., "pdf,jpg,png"
            $table->integer('max_file_size')->nullable(); // Max size in KB
            $table->boolean('is_required')->default(true);
            $table->integer('display_order')->default(0);
            
            // Audit trails
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Prevent duplicate field_name in the same track
            $table->unique(['spmb_track_id', 'field_name'], 'spmb_track_form_field_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spmb_track_form_fields');
    }
};
