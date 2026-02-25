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
        Schema::table('senjatas', function (Blueprint $table) {
            $table->date('masa_berlaku_simsa')->nullable()->after('nrp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('senjatas', function (Blueprint $table) {
            $table->dropColumn('masa_berlaku_simsa');
        });
    }
};
