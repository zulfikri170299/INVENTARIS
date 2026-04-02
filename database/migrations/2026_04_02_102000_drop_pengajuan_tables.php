<?php
/*
 * Created At: 2026-04-02T02:20:00Z
 */

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
        Schema::dropIfExists('dokumen_pengajuan');
        Schema::dropIfExists('riwayat_pengajuan');
        Schema::dropIfExists('pengajuan_berkas');
        Schema::dropIfExists('persyaratan_berkas');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No turning back for permanent removal as requested
    }
};
