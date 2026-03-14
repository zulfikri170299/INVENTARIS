<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('persyaratan_berkas', function (Blueprint $table) {
            $table->id();
            $table->enum('kategori', ['penghapusan', 'penetapan_status']);
            $table->string('nama_persyaratan');
            $table->text('deskripsi')->nullable();
            $table->boolean('wajib')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('persyaratan_berkas');
    }
};
