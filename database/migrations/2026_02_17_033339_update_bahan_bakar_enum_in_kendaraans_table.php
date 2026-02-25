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
        // Drop and recreate because SQLite doesn't support changing enums (CHECK constraints) easily
        // and the table is confirmed empty.
        Schema::dropIfExists('kendaraans');
        
        Schema::create('kendaraans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('satker_id')->constrained('satkers')->cascadeOnDelete();
            $table->string('jenis_kendaraan');
            $table->string('nup')->nullable();
            $table->string('no_rangka')->nullable();
            $table->string('nopol')->nullable();
            $table->enum('kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat']);
            $table->enum('bahan_bakar', ['Pertalite', 'Pertamax', 'Pertamina Dex', 'Listrik']);
            $table->string('penanggung_jawab')->nullable();
            $table->string('nrp')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No simple way to revert to old enum in SQLite without drop/recreate,
        // but since this is fixing a breakage, down can just drop.
        Schema::dropIfExists('kendaraans');
    }
};
