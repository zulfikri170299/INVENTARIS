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
        // 1. Explicitly drop problematic indexes in SQLite's global namespace
        try {
            DB::statement('DROP INDEX IF EXISTS users_email_unique');
            DB::statement('DROP INDEX IF EXISTS users_satker_id_index');
        } catch (\Exception $e) {}

        // 2. Create temporary table with desired schema
        Schema::dropIfExists('users_standardized');
        Schema::create('users_standardized', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email'); 
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->enum('role', ['Super Admin', 'Admin Satker', 'Pimpinan'])->default('Admin Satker');
            $table->unsignedBigInteger('satker_id')->nullable();
            $table->timestamps();
        });

        // 3. Migrate data from any existing table structure
        $sourceTable = null;
        if (Schema::hasTable('users')) {
            $sourceTable = 'users';
        } elseif (Schema::hasTable('users_temp')) {
            $sourceTable = 'users_temp';
        }

        if ($sourceTable) {
            $users = DB::table($sourceTable)->get();
            foreach ($users as $user) {
                $newRole = match ($user->role) {
                    'super_admin' => 'Super Admin',
                    'admin_satker' => 'Admin Satker',
                    'pimpinan' => 'Pimpinan',
                    default => $user->role,
                };

                if (!in_array($newRole, ['Super Admin', 'Admin Satker', 'Pimpinan'])) {
                    $newRole = 'Admin Satker';
                }

                DB::table('users_standardized')->insert([
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                    'password' => $user->password,
                    'remember_token' => $user->remember_token,
                    'role' => $newRole,
                    'satker_id' => $user->satker_id,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]);
            }

            // 4. Drop source table and its remaining indexes
            Schema::dropIfExists($sourceTable);
        }

        // 5. Rename to final table name
        Schema::rename('users_standardized', 'users');

        // 6. Re-add indexes back (SQLite index names are global, so this is safe now)
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
        Schema::dropIfExists('users');
    }
};
