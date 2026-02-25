<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('senjatas', function (Blueprint $table) {
            $table->string('jenis_amunisi_dibawa')->nullable()->after('status_penyimpanan');
            $table->integer('jumlah_amunisi_dibawa')->nullable()->default(0)->after('jenis_amunisi_dibawa');
        });
    }

    public function down(): void
    {
        Schema::table('senjatas', function (Blueprint $table) {
            $table->dropColumn(['jenis_amunisi_dibawa', 'jumlah_amunisi_dibawa']);
        });
    }
};
