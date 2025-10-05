<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    // Créer user_id admin pour la table FLUX
    // PROBLEME: J'ai déjà un user_id = 1 dans la table users et je ne peux pas le supprimer car il a des covoits... Donc, j'ai mis user_id = 15 pour l'admin (à changer si je le met en ligne)!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    public function run(): void
    {
        $adminExists = DB::table('users')->where('user_id', 1)->exists();

        if (!$adminExists) {
            DB::table('users')->insert([
                'user_id' => 1,
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
            ]);

            $this->command->info('Utilisateur admin créé avec succès !');
            $this->command->info('User ID: 1');
            $this->command->info('Email: admin@ecoride.fr');
            $this->command->info('Mot de passe: Admin@EcoRide2024!');
        } else {
            $this->command->info('L\'utilisateur admin existe déjà.');
        }
    }
}