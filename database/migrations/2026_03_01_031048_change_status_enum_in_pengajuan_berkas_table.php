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
        Schema::table('pengajuan_berkas', function (Blueprint $table) {
            $table->enum('status', [
                'diajukan', 'diterima', 'diproses', 'dikembalikan',
                'naik_ke_kapolda', 'ditandatangani', 'selesai'
            ])->default('diajukan')->change();
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_berkas', function (Blueprint $table) {
            $table->enum('status', [
                'diajukan', 'diterima', 'dikembalikan',
                'naik_ke_kapolda', 'ditandatangani', 'selesai'
            ])->default('diajukan')->change();
        });
    }
};
