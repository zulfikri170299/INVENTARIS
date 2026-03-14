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
        Schema::table('persyaratan_berkas', function (Blueprint $table) {
            $table->string('file_contoh')->nullable()->after('wajib');
            $table->string('nama_file_contoh')->nullable()->after('file_contoh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('persyaratan_berkas', function (Blueprint $table) {
            $table->dropColumn(['file_contoh', 'nama_file_contoh']);
        });
    }
};
