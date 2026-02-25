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
        // 1. Check if users table is empty
        $userCount = DB::table('users')->count();
        
        if ($userCount === 0) {
            // 2. Identify source of data
            $sourceTable = null;
            if (Schema::hasTable('users_old') && DB::table('users_old')->count() > 0) {
                $sourceTable = 'users_old';
            } elseif (Schema::hasTable('users_temp') && DB::table('users_temp')->count() > 0) {
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

                    // Final fallback to ensure valid enum
                    if (!in_array($newRole, ['Super Admin', 'Admin Satker', 'Pimpinan'])) {
                        $newRole = 'Admin Satker';
                    }

                    DB::table('users')->insert([
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
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down needed for emergency restore
    }
};
