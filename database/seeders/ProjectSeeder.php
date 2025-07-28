<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Client;
use App\Models\User;
use Carbon\Carbon;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assicuriamoci che ci siano clienti e utenti nel database
        $clients = Client::all();
        $users = User::all();

        if ($clients->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Assicurati che ci siano clienti e utenti nel database prima di eseguire il ProjectSeeder');
            return;
        }

        $projects = [
            [
                'name' => 'Sviluppo E-commerce Fashion',
                'description' => 'Piattaforma e-commerce completa per vendita abbigliamento online con sistema di pagamento integrato, gestione inventario e dashboard amministrativa.',
                'client_id' => $clients->random()->id,
                'user_id' => $users->random()->id,
                'status' => 'active',
                'priority' => 'high',
                'start_date' => Carbon::now()->subDays(30),
                'end_date' => Carbon::now()->addDays(60),
                'deadline' => Carbon::now()->addDays(75),
                'budget' => 25000.00,
                'estimated_cost' => 22000.00,
                'actual_cost' => 15000.00,
                'progress_percentage' => 65,
                'notes' => 'Progetto in corso, fase di sviluppo frontend completata. Prossimo step: integrazione API pagamenti.',
                'metadata' => [
                    'technologies' => ['Laravel', 'Vue.js', 'MySQL', 'Stripe'],
                    'team_size' => 4,
                    'repository' => 'https://github.com/client/ecommerce-fashion'
                ]
            ],
            [
                'name' => 'App Mobile Delivery',
                'description' => 'Applicazione mobile per servizio di delivery con tracking in tempo reale, sistema di rating e notifiche push.',
                'client_id' => $clients->random()->id,
                'user_id' => $users->random()->id,
                'status' => 'completed',
                'priority' => 'medium',
                'start_date' => Carbon::now()->subDays(120),
                'end_date' => Carbon::now()->subDays(10),
                'deadline' => Carbon::now()->subDays(5),
                'budget' => 18000.00,
                'estimated_cost' => 16500.00,
                'actual_cost' => 17200.00,
                'progress_percentage' => 100,
                'notes' => 'Progetto completato con successo. App pubblicata su App Store e Google Play.',
                'metadata' => [
                    'technologies' => ['React Native', 'Node.js', 'MongoDB', 'Firebase'],
                    'team_size' => 3,
                    'downloads' => 1250
                ]
            ],
            [
                'name' => 'Sistema CRM Aziendale',
                'description' => 'Sistema di gestione clienti personalizzato con automazione marketing, pipeline vendite e reportistica avanzata.',
                'client_id' => $clients->random()->id,
                'user_id' => $users->random()->id,
                'status' => 'draft',
                'priority' => 'urgent',
                'start_date' => Carbon::now()->addDays(7),
                'end_date' => Carbon::now()->addDays(90),
                'deadline' => Carbon::now()->addDays(100),
                'budget' => 35000.00,
                'estimated_cost' => 32000.00,
                'actual_cost' => 0.00,
                'progress_percentage' => 0,
                'notes' => 'Progetto in fase di pianificazione. Meeting con cliente programmato per definire requisiti dettagliati.',
                'metadata' => [
                    'technologies' => ['Laravel', 'React', 'PostgreSQL', 'Redis'],
                    'team_size' => 5,
                    'modules' => ['Contacts', 'Sales', 'Marketing', 'Reports']
                ]
            ],
            [
                'name' => 'Portale Web Immobiliare',
                'description' => 'Piattaforma web per agenzia immobiliare con ricerca avanzata, virtual tour e sistema di prenotazione visite.',
                'client_id' => $clients->random()->id,
                'user_id' => $users->random()->id,
                'status' => 'on_hold',
                'priority' => 'low',
                'start_date' => Carbon::now()->subDays(45),
                'end_date' => Carbon::now()->addDays(30),
                'deadline' => Carbon::now()->addDays(45),
                'budget' => 15000.00,
                'estimated_cost' => 14000.00,
                'actual_cost' => 8000.00,
                'progress_percentage' => 40,
                'notes' => 'Progetto sospeso temporaneamente per revisione requisiti da parte del cliente.',
                'metadata' => [
                    'technologies' => ['WordPress', 'PHP', 'MySQL', 'JavaScript'],
                    'team_size' => 2,
                    'features' => ['Search', 'Virtual Tours', 'Booking System']
                ]
            ],
            [
                'name' => 'Dashboard Analytics',
                'description' => 'Dashboard interattiva per visualizzazione dati aziendali con grafici in tempo reale e export automatizzati.',
                'client_id' => $clients->random()->id,
                'user_id' => $users->random()->id,
                'status' => 'active',
                'priority' => 'medium',
                'start_date' => Carbon::now()->subDays(20),
                'end_date' => Carbon::now()->addDays(40),
                'deadline' => Carbon::now()->addDays(50),
                'budget' => 12000.00,
                'estimated_cost' => 11000.00,
                'actual_cost' => 6500.00,
                'progress_percentage' => 75,
                'notes' => 'Sviluppo in fase avanzata. Completata integrazione con API esterne.',
                'metadata' => [
                    'technologies' => ['Vue.js', 'D3.js', 'Laravel API', 'MySQL'],
                    'team_size' => 3,
                    'data_sources' => ['Google Analytics', 'Facebook Ads', 'Internal DB']
                ]
            ],
            [
                'name' => 'Sistema Gestione Magazzino',
                'description' => 'Software per gestione inventario con codici a barre, tracking movimenti e integrazione con sistemi contabili.',
                'client_id' => $clients->random()->id,
                'user_id' => $users->random()->id,
                'status' => 'cancelled',
                'priority' => 'medium',
                'start_date' => Carbon::now()->subDays(60),
                'end_date' => Carbon::now()->subDays(30),
                'deadline' => Carbon::now()->subDays(20),
                'budget' => 20000.00,
                'estimated_cost' => 18000.00,
                'actual_cost' => 5000.00,
                'progress_percentage' => 25,
                'notes' => 'Progetto cancellato per cambio priorità aziendali del cliente.',
                'metadata' => [
                    'technologies' => ['Laravel', 'MySQL', 'Barcode Scanner API'],
                    'team_size' => 2,
                    'reason_cancelled' => 'Budget constraints'
                ]
            ],
            [
                'name' => 'Piattaforma E-learning',
                'description' => 'Piattaforma per corsi online con video streaming, quiz interattivi e certificazioni.',
                'client_id' => $clients->random()->id,
                'user_id' => $users->random()->id,
                'status' => 'active',
                'priority' => 'high',
                'start_date' => Carbon::now()->subDays(15),
                'end_date' => Carbon::now()->addDays(75),
                'deadline' => Carbon::now()->addDays(85),
                'budget' => 30000.00,
                'estimated_cost' => 28000.00,
                'actual_cost' => 12000.00,
                'progress_percentage' => 45,
                'notes' => 'Sviluppo del sistema di autenticazione e gestione corsi completato.',
                'metadata' => [
                    'technologies' => ['Laravel', 'Vue.js', 'FFmpeg', 'AWS S3'],
                    'team_size' => 4,
                    'features' => ['Video Streaming', 'Quizzes', 'Certificates', 'Progress Tracking']
                ]
            ],
            [
                'name' => 'API Gateway Microservizi',
                'description' => 'Sviluppo API Gateway per architettura microservizi con autenticazione, rate limiting e monitoring.',
                'client_id' => $clients->random()->id,
                'user_id' => $users->random()->id,
                'status' => 'completed',
                'priority' => 'urgent',
                'start_date' => Carbon::now()->subDays(90),
                'end_date' => Carbon::now()->subDays(20),
                'deadline' => Carbon::now()->subDays(15),
                'budget' => 22000.00,
                'estimated_cost' => 20000.00,
                'actual_cost' => 21500.00,
                'progress_percentage' => 100,
                'notes' => 'Progetto completato. Sistema in produzione e funzionante correttamente.',
                'metadata' => [
                    'technologies' => ['Node.js', 'Docker', 'Kubernetes', 'Redis'],
                    'team_size' => 3,
                    'performance' => '99.9% uptime'
                ]
            ],
            [
                'name' => 'App Fitness Tracker',
                'description' => 'Applicazione mobile per tracking attività fisica con integrazione wearable e social features.',
                'client_id' => $clients->random()->id,
                'user_id' => $users->random()->id,
                'status' => 'active',
                'priority' => 'medium',
                'start_date' => Carbon::now()->subDays(25),
                'end_date' => Carbon::now()->addDays(50),
                'deadline' => Carbon::now()->addDays(60),
                'budget' => 16000.00,
                'estimated_cost' => 15000.00,
                'actual_cost' => 9000.00,
                'progress_percentage' => 55,
                'notes' => 'Integrazione con dispositivi wearable completata. In corso sviluppo features social.',
                'metadata' => [
                    'technologies' => ['Flutter', 'Firebase', 'HealthKit', 'Google Fit'],
                    'team_size' => 3,
                    'integrations' => ['Apple Watch', 'Fitbit', 'Garmin']
                ]
            ],
            [
                'name' => 'Sistema Prenotazioni Online',
                'description' => 'Piattaforma per prenotazioni online con calendario dinamico, pagamenti e notifiche automatiche.',
                'client_id' => $clients->random()->id,
                'user_id' => $users->random()->id,
                'status' => 'draft',
                'priority' => 'low',
                'start_date' => Carbon::now()->addDays(14),
                'end_date' => Carbon::now()->addDays(80),
                'deadline' => Carbon::now()->addDays(90),
                'budget' => 14000.00,
                'estimated_cost' => 13000.00,
                'actual_cost' => 0.00,
                'progress_percentage' => 0,
                'notes' => 'Progetto in fase di analisi requisiti. Definizione architettura in corso.',
                'metadata' => [
                    'technologies' => ['Laravel', 'Vue.js', 'MySQL', 'Stripe'],
                    'team_size' => 2,
                    'target_sectors' => ['Healthcare', 'Beauty', 'Professional Services']
                ]
            ]
        ];

        foreach ($projects as $projectData) {
            Project::create($projectData);
        }

        $this->command->info('ProjectSeeder completato: ' . count($projects) . ' progetti creati.');
    }
}
