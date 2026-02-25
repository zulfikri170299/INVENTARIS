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
        Schema::create('kendaraans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('satker_id')->constrained('satkers')->cascadeOnDelete();
            $table->string('jenis_kendaraan');
            $table->string('nup');
            $table->string('no_rangka');
            $table->string('nopol');
            $table->enum('kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat']);
            $table->enum('bahan_bakar', ['Pertalite', 'Pertamax', 'Pertamina Dex', 'Listrik']);
            $table->string('penanggung_jawab');
            $table->string('nrp');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kendaraans');
    }
};
