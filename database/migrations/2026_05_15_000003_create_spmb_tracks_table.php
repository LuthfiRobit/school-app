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
        Schema::create('spmb_tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spmb_configuration_id')->constrained('spmb_configurations')->cascadeOnDelete();
            $table->foreignId('master_track_type_id')->constrained('master_track_types')->cascadeOnDelete();
            $table->unsignedInteger('quota')->default(0);
            $table->decimal('registration_fee', 15, 2)->default(0);
            $table->enum('payment_mode', ['PRE_PAYMENT', 'POST_PAYMENT'])->default('PRE_PAYMENT');
            $table->boolean('allow_carryover')->default(false);
            $table->dateTime('announcement_date')->nullable();
            $table->enum('status', ['active', 'closed', 'full'])->default('active');
            
            // Audit trails
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Prevent duplicate tracks for the same academic year config
            $table->unique(['spmb_configuration_id', 'master_track_type_id'], 'spmb_track_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spmb_tracks');
    }
};
