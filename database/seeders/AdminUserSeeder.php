<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $query = DB::table('users');
        $admin = null;

        if (Schema::hasColumn('users', 'email')) {
            $admin = (clone $query)->where('email', 'admin@ecoride.fr')->first();
        }

        if (!$admin && Schema::hasColumn('users', 'role')) {
            $admin = (clone $query)->where('role', 'Admin')->orderBy('user_id')->first();
        }

        if (!$admin && Schema::hasColumn('users', 'user_id')) {
            $admin = (clone $query)->where('user_id', 1)->first();
        }

        if (!$admin) {
            $data = [
                'name' => 'ADMIN',
                'password' => Hash::make('Admin@EcoRide2024!'),
                'role' => 'Admin',
                'n_credit' => 0,
                'photo' => null,
                'phototype' => null,
                'pref_smoke' => null,
                'pref_pet' => null,
                'pref_libre' => null,
            ];

            if (Schema::hasColumn('users', 'email')) {
                $data['email'] = 'admin@ecoride.fr';
            }

            $adminId = null;

            if (!DB::table('users')->where('user_id', 1)->exists()) {
                $data['user_id'] = 1;
                DB::table('users')->insert($data);
                $adminId = 1;
            } else {
                $adminId = DB::table('users')->insertGetId($data, 'user_id');
            }

            $this->command->info('Utilisateur admin cree avec succes !');
            $this->command->info('User ID: ' . $adminId);
            $this->command->info('Email: admin@ecoride.fr');
            $this->command->info('Mot de passe: Admin@EcoRide2024!');
        } else {
            $updates = ['role' => 'Admin'];

            if (Schema::hasColumn('users', 'email')) {
                $updates['email'] = $admin->email ?: 'admin@ecoride.fr';
            }

            DB::table('users')
                ->where('user_id', $admin->user_id)
                ->update($updates);

            $this->command->info('L\'utilisateur admin existe deja.');
        }
    }
}
