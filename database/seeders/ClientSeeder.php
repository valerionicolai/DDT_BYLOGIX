<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ClientContact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientsData = [
            [
                'client' => [
                    'company' => 'Acme Corporation',
                    'vat_number' => 'IT12345678901',
                    'type' => 'cliente',
                    'address' => 'Via Milano 123',
                    'city' => 'Milano',
                    'postal_code' => '20100',
                    'country' => 'Italia',
                    'status' => 'active',
                    'notes' => 'Cliente principale per progetti di sviluppo software',
                    // Campi legacy per compatibilità
                    'name' => 'Mario Rossi',
                    'email' => 'mario.rossi@acmecorp.com',
                    'phone' => '+39 02 1234567',
                ],
                'contacts' => [
                    [
                        'name' => 'Mario Rossi',
                        'area_role' => 'CEO',
                        'email' => 'mario.rossi@acmecorp.com',
                        'phone' => '+39 02 1234567',
                        'is_primary' => true,
                    ],
                    [
                        'name' => 'Anna Verdi',
                        'area_role' => 'CTO',
                        'email' => 'anna.verdi@acmecorp.com',
                        'phone' => '+39 02 1234568',
                        'is_primary' => false,
                    ],
                    [
                        'name' => 'Luca Bianchi',
                        'area_role' => 'Project Manager',
                        'email' => 'luca.bianchi@acmecorp.com',
                        'phone' => '+39 02 1234569',
                        'is_primary' => false,
                    ],
                ],
            ],
            [
                'client' => [
                     'company' => 'TechStart S.r.l.',
                     'vat_number' => 'IT98765432109',
                     'type' => 'cliente',
                    'address' => 'Via Roma 456',
                    'city' => 'Roma',
                    'postal_code' => '00100',
                    'country' => 'Italia',
                    'status' => 'active',
                    'notes' => 'Startup tecnologica specializzata in AI',
                    // Campi legacy
                    'name' => 'Laura Bianchi',
                    'email' => 'laura.bianchi@techstart.it',
                    'phone' => '+39 06 9876543',
                ],
                'contacts' => [
                    [
                        'name' => 'Laura Bianchi',
                        'area_role' => 'Founder & CEO',
                        'email' => 'laura.bianchi@techstart.it',
                        'phone' => '+39 06 9876543',
                        'is_primary' => true,
                    ],
                    [
                        'name' => 'Marco Neri',
                        'area_role' => 'Lead Developer',
                        'email' => 'marco.neri@techstart.it',
                        'phone' => '+39 06 9876544',
                        'is_primary' => false,
                    ],
                ],
            ],
            [
                'client' => [
                     'company' => 'Digital Solutions Ltd',
                     'vat_number' => 'GB123456789',
                     'type' => 'cliente',
                    'address' => '123 Tech Street',
                    'city' => 'London',
                    'postal_code' => 'SW1A 1AA',
                    'country' => 'United Kingdom',
                    'status' => 'active',
                    'notes' => 'Cliente internazionale per progetti web',
                    // Campi legacy
                    'name' => 'John Smith',
                    'email' => 'john.smith@digitalsolutions.com',
                    'phone' => '+44 20 1234 5678',
                ],
                'contacts' => [
                    [
                        'name' => 'John Smith',
                        'area_role' => 'Managing Director',
                        'email' => 'john.smith@digitalsolutions.com',
                        'phone' => '+44 20 1234 5678',
                        'is_primary' => true,
                    ],
                    [
                        'name' => 'Emma Wilson',
                        'area_role' => 'Sales Manager',
                        'email' => 'emma.wilson@digitalsolutions.com',
                        'phone' => '+44 20 1234 5679',
                        'is_primary' => false,
                    ],
                    [
                        'name' => 'David Brown',
                        'area_role' => 'Technical Lead',
                        'email' => 'david.brown@digitalsolutions.com',
                        'phone' => '+44 20 1234 5680',
                        'is_primary' => false,
                    ],
                ],
            ],
            [
                'client' => [
                     'company' => 'Innovate Inc.',
                     'vat_number' => 'US987654321',
                     'type' => 'cliente',
                    'address' => '789 Innovation Ave',
                    'city' => 'San Francisco',
                    'postal_code' => '94102',
                    'country' => 'USA',
                    'status' => 'inactive',
                    'notes' => 'Cliente in pausa per ristrutturazione aziendale',
                    // Campi legacy
                    'name' => 'Sarah Johnson',
                    'email' => 'sarah.johnson@innovate.com',
                    'phone' => '+1 555 123 4567',
                ],
                'contacts' => [
                    [
                        'name' => 'Sarah Johnson',
                        'area_role' => 'VP Operations',
                        'email' => 'sarah.johnson@innovate.com',
                        'phone' => '+1 555 123 4567',
                        'is_primary' => true,
                    ],
                ],
            ],
            [
                'client' => [
                     'company' => 'Green Energy S.p.A.',
                     'vat_number' => 'IT11223344556',
                     'type' => 'fornitore',
                    'address' => 'Corso Torino 789',
                    'city' => 'Torino',
                    'postal_code' => '10100',
                    'country' => 'Italia',
                    'status' => 'active',
                    'notes' => 'Fornitore leader nel settore energie rinnovabili',
                    // Campi legacy
                    'name' => 'Giuseppe Verdi',
                    'email' => 'giuseppe.verdi@greenenergy.it',
                    'phone' => '+39 011 5555555',
                ],
                'contacts' => [
                    [
                        'name' => 'Giuseppe Verdi',
                        'area_role' => 'Direttore Commerciale',
                        'email' => 'giuseppe.verdi@greenenergy.it',
                        'phone' => '+39 011 5555555',
                        'is_primary' => true,
                    ],
                    [
                        'name' => 'Francesca Rossi',
                        'area_role' => 'Responsabile Acquisti',
                        'email' => 'francesca.rossi@greenenergy.it',
                        'phone' => '+39 011 5555556',
                        'is_primary' => false,
                    ],
                    [
                        'name' => 'Roberto Blu',
                        'area_role' => 'Responsabile Tecnico',
                        'email' => 'roberto.blu@greenenergy.it',
                        'phone' => '+39 011 5555557',
                        'is_primary' => false,
                    ],
                    [
                        'name' => 'Chiara Gialli',
                        'area_role' => 'Amministrazione',
                        'email' => 'chiara.gialli@greenenergy.it',
                        'phone' => '+39 011 5555558',
                        'is_primary' => false,
                    ],
                ],
            ],
            [
                'client' => [
                     'company' => 'Fashion Forward',
                     'vat_number' => 'IT55667788990',
                     'type' => 'cliente',
                    'address' => 'Via della Moda 321',
                    'city' => 'Firenze',
                    'postal_code' => '50100',
                    'country' => 'Italia',
                    'status' => 'active',
                    'notes' => 'Nuovo cliente in fase di valutazione',
                    // Campi legacy
                    'name' => 'Giulia Neri',
                    'email' => 'giulia.neri@fashionforward.com',
                    'phone' => '+39 055 7777777',
                ],
                'contacts' => [
                    [
                        'name' => 'Giulia Neri',
                        'area_role' => 'Creative Director',
                        'email' => 'giulia.neri@fashionforward.com',
                        'phone' => '+39 055 7777777',
                        'is_primary' => true,
                    ],
                    [
                        'name' => 'Alessandro Viola',
                        'area_role' => 'Marketing Manager',
                        'email' => 'alessandro.viola@fashionforward.com',
                        'phone' => '+39 055 7777778',
                        'is_primary' => false,
                    ],
                ],
            ],
            [
                'client' => [
                     'company' => 'Steel Works Italia S.r.l.',
                     'vat_number' => 'IT99887766554',
                     'type' => 'fornitore',
                    'address' => 'Via Industriale 88',
                    'city' => 'Brescia',
                    'postal_code' => '25100',
                    'country' => 'Italia',
                    'status' => 'active',
                    'notes' => 'Fornitore di materiali metallici e componenti industriali',
                    // Campi legacy
                    'name' => 'Antonio Ferro',
                    'email' => 'antonio.ferro@steelworks.it',
                    'phone' => '+39 030 1111111',
                ],
                'contacts' => [
                    [
                        'name' => 'Antonio Ferro',
                        'area_role' => 'Direttore Generale',
                        'email' => 'antonio.ferro@steelworks.it',
                        'phone' => '+39 030 1111111',
                        'is_primary' => true,
                    ],
                    [
                        'name' => 'Silvia Acciaio',
                        'area_role' => 'Responsabile Vendite',
                        'email' => 'silvia.acciaio@steelworks.it',
                        'phone' => '+39 030 1111112',
                        'is_primary' => false,
                    ],
                    [
                        'name' => 'Marco Metallo',
                        'area_role' => 'Responsabile Produzione',
                        'email' => 'marco.metallo@steelworks.it',
                        'phone' => '+39 030 1111113',
                        'is_primary' => false,
                    ],
                    [
                        'name' => 'Elena Ferro',
                        'area_role' => 'Controllo Qualità',
                        'email' => 'elena.ferro@steelworks.it',
                        'phone' => '+39 030 1111114',
                        'is_primary' => false,
                    ],
                    [
                        'name' => 'Paolo Zinco',
                        'area_role' => 'Logistica',
                        'email' => 'paolo.zinco@steelworks.it',
                        'phone' => '+39 030 1111115',
                        'is_primary' => false,
                    ],
                ],
            ],
        ];

        foreach ($clientsData as $data) {
            // Crea o trova il cliente
            $client = Client::firstOrCreate(
                ['company' => $data['client']['company']],
                array_merge($data['client'], [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );

            // Crea i contatti per questo cliente
            foreach ($data['contacts'] as $contactData) {
                ClientContact::firstOrCreate(
                    [
                        'client_id' => $client->id,
                        'email' => $contactData['email']
                    ],
                    array_merge($contactData, [
                        'client_id' => $client->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])
                );
            }
        }
    }
}
