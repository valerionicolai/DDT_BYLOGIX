<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = [
            [
                'name' => 'Mario Rossi',
                'email' => 'mario.rossi@acmecorp.com',
                'phone' => '+39 02 1234567',
                'company' => 'Acme Corporation',
                'address' => 'Via Milano 123',
                'city' => 'Milano',
                'postal_code' => '20100',
                'country' => 'Italia',
                'status' => 'active',
                'notes' => 'Cliente principale per progetti di sviluppo software',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Laura Bianchi',
                'email' => 'laura.bianchi@techstart.it',
                'phone' => '+39 06 9876543',
                'company' => 'TechStart S.r.l.',
                'address' => 'Via Roma 456',
                'city' => 'Roma',
                'postal_code' => '00100',
                'country' => 'Italia',
                'status' => 'active',
                'notes' => 'Startup tecnologica specializzata in AI',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'John Smith',
                'email' => 'john.smith@digitalsolutions.com',
                'phone' => '+44 20 1234 5678',
                'company' => 'Digital Solutions Ltd',
                'address' => '123 Tech Street',
                'city' => 'London',
                'postal_code' => 'SW1A 1AA',
                'country' => 'United Kingdom',
                'status' => 'active',
                'notes' => 'Cliente internazionale per progetti web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@innovate.com',
                'phone' => '+1 555 123 4567',
                'company' => 'Innovate Inc.',
                'address' => '789 Innovation Ave',
                'city' => 'San Francisco',
                'postal_code' => '94102',
                'country' => 'USA',
                'status' => 'inactive',
                'notes' => 'Cliente in pausa per ristrutturazione aziendale',
                'created_at' => now()->subMonths(3),
                'updated_at' => now()->subWeeks(2),
            ],
            [
                'name' => 'Giuseppe Verdi',
                'email' => 'giuseppe.verdi@greenenergy.it',
                'phone' => '+39 011 5555555',
                'company' => 'Green Energy S.p.A.',
                'address' => 'Corso Torino 789',
                'city' => 'Torino',
                'postal_code' => '10100',
                'country' => 'Italia',
                'status' => 'active',
                'notes' => 'Azienda leader nel settore energie rinnovabili',
                'created_at' => now()->subMonths(1),
                'updated_at' => now()->subDays(5),
            ],
            [
                'name' => 'Giulia Neri',
                'email' => 'giulia.neri@fashionforward.com',
                'phone' => '+39 055 7777777',
                'company' => 'Fashion Forward',
                'address' => 'Via della Moda 321',
                'city' => 'Firenze',
                'postal_code' => '50100',
                'country' => 'Italia',
                'status' => 'active',
                'notes' => 'Nuovo cliente in fase di valutazione',
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(2),
            ],
        ];

        foreach ($clients as $client) {
            Client::firstOrCreate(
                ['email' => $client['email']], // Find by email
                $client // Create with all data if not found
            );
        }
    }
}
