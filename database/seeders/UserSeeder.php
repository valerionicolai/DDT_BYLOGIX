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
        // Crea un utente amministratore di test
        User::create([
            'name' => 'Admin DTT',
            'email' => 'admin@dttbylogix.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        // Crea un utente normale di test
        User::create([
            'name' => 'Mario Rossi',
            'email' => 'mario.rossi@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        // Crea un altro utente di test
        User::create([
            'name' => 'Giulia Bianchi',
            'email' => 'giulia.bianchi@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
    }
}
