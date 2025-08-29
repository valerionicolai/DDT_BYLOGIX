<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crea o aggiorna un utente amministratore di test
        User::updateOrCreate(
            ['email' => 'admin@ddtbylogix.com'],
            [
                'name' => 'Admin DDT',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'role' => 'admin',
            ]
        );

        // Crea o aggiorna un utente normale di test
        User::updateOrCreate(
            ['email' => 'mario.rossi@example.com'],
            [
                'name' => 'Mario Rossi',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'role' => 'user',
            ]
        );

        // Crea o aggiorna un altro utente di test
        User::updateOrCreate(
            ['email' => 'giulia.bianchi@example.com'],
            [
                'name' => 'Giulia Bianchi',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'role' => 'user',
            ]
        );

        // Crea o aggiorna un secondo amministratore
        User::updateOrCreate(
            ['email' => 'superadmin@dttbylogix.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'role' => 'admin',
            ]
        );

        // Crea o aggiorna utenti aggiuntivi per test
        User::updateOrCreate(
            ['email' => 'luca.verdi@example.com'],
            [
                'name' => 'Luca Verdi',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'role' => 'user',
            ]
        );

        echo "UserSeeder completato: 5 utenti creati/aggiornati (2 admin, 3 user).\n";
    }
}
