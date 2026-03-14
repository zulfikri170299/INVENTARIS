<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_berkas', function (Blueprint $table) {
            $table->id();
            $table->enum('kategori', ['penghapusan', 'penetapan_status']);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('satker_id')->constrained()->onDelete('cascade');
            $table->string('judul');
            $table->text('keterangan')->nullable();
            $table->enum('status', [
                'diajukan', 'diterima', 'dikembalikan',
                'naik_ke_kapolda', 'ditandatangani', 'selesai'
            ])->default('diajukan');
            $table->text('catatan_super_admin')->nullable();
            $table->string('berkas_final')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_berkas');
    }
};
