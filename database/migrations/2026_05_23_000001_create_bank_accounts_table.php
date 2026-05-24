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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name');                    // BCA, BRI, Mandiri, BSI, dll.
            $table->string('account_number', 30);           // Nomor rekening
            $table->string('account_holder');               // Atas nama
            $table->string('branch')->nullable();           // Nama cabang
            $table->text('description')->nullable();        // Keterangan penggunaan
            $table->string('logo_path')->nullable();        // Path logo bank (untuk UI)
            $table->tinyInteger('is_active')->default(1);   // Status aktif/nonaktif
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
