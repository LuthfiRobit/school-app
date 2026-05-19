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
        Schema::create('spmb_track_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spmb_track_id')->constrained('spmb_tracks')->cascadeOnDelete();
            $table->foreignId('master_fee_component_id')->constrained('master_fee_components')->cascadeOnDelete();
            $table->decimal('amount', 15, 2)->default(0);
            $table->enum('category', ['registration', 're_registration'])->default('re_registration');
            $table->integer('display_order')->default(0);
            
            // Audit trails
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Prevent duplicate fee components in the same track
            $table->unique(['spmb_track_id', 'master_fee_component_id'], 'spmb_track_fee_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spmb_track_fees');
    }
};
