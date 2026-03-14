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
        Schema::table('kendaraans', function (Blueprint $table) {
            $table->string('nup')->nullable()->change();
            $table->string('no_rangka')->nullable()->change();
            $table->string('nopol')->nullable()->change();
            $table->string('penanggung_jawab')->nullable()->change();
            $table->string('nrp')->nullable()->change();
        });

        Schema::table('alsuses', function (Blueprint $table) {
            $table->string('nup')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kendaraans', function (Blueprint $table) {
            $table->string('nup')->nullable(false)->change();
            $table->string('no_rangka')->nullable(false)->change();
            $table->string('nopol')->nullable(false)->change();
            $table->string('penanggung_jawab')->nullable(false)->change();
            $table->string('nrp')->nullable(false)->change();
        });

        Schema::table('alsuses', function (Blueprint $table) {
            $table->string('nup')->nullable(false)->change();
        });
    }
};
