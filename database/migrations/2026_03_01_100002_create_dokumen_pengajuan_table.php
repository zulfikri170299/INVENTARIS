<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen_pengajuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_berkas_id')->constrained()->onDelete('cascade');
            $table->foreignId('persyaratan_berkas_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->string('nama_file');
            $table->boolean('terverifikasi')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_pengajuan');
    }
};
