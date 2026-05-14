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
        Schema::create('school_identities', function (Blueprint $table) {
            $table->id();
            
            // 1. Core Identity
            $table->string('school_name');
            $table->string('npsn', 20)->nullable();
            $table->string('education_level', 20)->nullable(); // SD, SMP, etc.
            $table->string('school_status', 20)->nullable(); // Negeri, Swasta
            $table->string('ownership_status')->nullable();
            
            // 2. Legality
            $table->string('establishment_sk')->nullable();
            $table->date('establishment_date')->nullable();
            $table->string('operational_sk')->nullable();
            $table->string('tax_id')->nullable(); // NPWP
            $table->string('accreditation', 10)->nullable();
            $table->date('accreditation_expiry_date')->nullable();
            
            // 3. Contact & Location
            $table->text('address')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            
            // 4. Signatories
            $table->string('headmaster_name')->nullable();
            $table->string('headmaster_nip')->nullable();
            $table->string('treasurer_name')->nullable();
            $table->string('treasurer_nip')->nullable();
            $table->string('operator_name')->nullable();
            $table->string('operator_nip')->nullable();
            
            // 5. Visual Assets (Store paths)
            $table->string('logo')->nullable();
            $table->string('stamp')->nullable();
            $table->string('profile_image')->nullable();
            
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
        Schema::dropIfExists('school_identities');
    }
};
