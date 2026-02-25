<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite handle for dropping indexes
        try {
            DB::statement('DROP INDEX IF EXISTS users_email_unique');
            DB::statement('DROP INDEX IF EXISTS users_satker_id_index');
        } catch (\Exception $e) {}

        // 1. Create temporary table with role as string
        Schema::create('users_temp_rebuild', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('role')->default('Admin Satker');
            $table->unsignedBigInteger('satker_id')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });

        // 2. Copy data
        if (Schema::hasTable('users')) {
            $users = DB::table('users')->get();
            foreach ($users as $user) {
                DB::table('users_temp_rebuild')->insert([
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                    'password' => $user->password,
                    'remember_token' => $user->remember_token,
                    'role' => $user->role,
                    'satker_id' => $user->satker_id,
                    'last_seen_at' => $user->last_seen_at ?? null,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]);
            }
            Schema::dropIfExists('users');
        }

        // 3. Finalize
        Schema::rename('users_temp_rebuild', 'users');

        Schema::table('users', function (Blueprint $table) {
            $table->unique('email');
            $table->index('satker_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting to enum is complex in SQLite rebuilding, 
        // but since string is more flexible, we'll keep it simple.
    }
};
