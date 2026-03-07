<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = DB::table('users')->where('email', 'admin@ecoride.fr')->first();

        if (!$admin) {
            $data = [
                'name' => 'ADMIN',
                'email' => 'admin@ecoride.fr',
                'password' => Hash::make('Admin@EcoRide2024!'),
                'role' => 'Admin',
                'n_credit' => 0,
                'photo' => null,
                'phototype' => null,
                'pref_smoke' => null,
                'pref_pet' => null,
                'pref_libre' => null,
            ];

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
            DB::table('users')
                ->where('email', 'admin@ecoride.fr')
                ->update(['role' => 'Admin']);

            $this->command->info('L\'utilisateur admin existe deja.');
        }
    }
}
