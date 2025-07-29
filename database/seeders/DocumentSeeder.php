<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ottieni alcuni record esistenti per le relazioni
        $users = User::all();
        $clients = Client::all();
        $projects = Project::all();

        if ($users->isEmpty()) {
            $this->command->warn('Nessun utente trovato. Assicurati di aver eseguito il UserSeeder prima.');
            return;
        }

        $documentTypes = [
            'Fattura',
            'Contratto',
            'Preventivo',
            'Ordine di acquisto',
            'Ricevuta',
            'Documento tecnico',
            'Specifica',
            'Manuale',
            'Certificato',
            'Relazione'
        ];

        $suppliers = [
            'Fornitore A S.r.l.',
            'Azienda Beta',
            'Gamma Solutions',
            'Delta Tech',
            'Epsilon Services',
            'Zeta Materials',
            'Eta Consulting',
            'Theta Industries',
            'Iota Systems',
            'Kappa Group'
        ];

        $statuses = ['draft', 'active', 'archived'];

        // Crea 50 documenti di test
        for ($i = 1; $i <= 50; $i++) {
            $documentType = $documentTypes[array_rand($documentTypes)];
            $supplier = $suppliers[array_rand($suppliers)];
            $status = $statuses[array_rand($statuses)];
            $user = $users->random();
            $client = $clients->isNotEmpty() ? $clients->random() : null;
            $project = $projects->isNotEmpty() ? $projects->random() : null;

            Document::create([
                'title' => "Documento {$documentType} #{$i}",
                'description' => "Descrizione dettagliata del documento {$documentType} numero {$i}. Questo documento contiene informazioni importanti relative al progetto.",
                'document_type' => $documentType,
                'supplier_name' => $supplier,
                'file_path' => "documents/{$i}/document_{$i}.pdf",
                'file_name' => "document_{$i}.pdf",
                'file_size' => rand(100, 5000) . ' KB',
                'mime_type' => 'application/pdf',
                'project_id' => $project?->id,
                'client_id' => $client?->id,
                'user_id' => $user->id,
                'status' => $status,
                'document_date' => Carbon::now()->subDays(rand(1, 365)),
                'amount' => $documentType === 'Fattura' || $documentType === 'Preventivo' ? rand(500, 50000) : null,
                'reference_number' => strtoupper($documentType[0]) . str_pad($i, 4, '0', STR_PAD_LEFT),
                'metadata' => [
                    'version' => '1.0',
                    'category' => $documentType,
                    'priority' => ['low', 'medium', 'high'][array_rand(['low', 'medium', 'high'])],
                    'tags' => ['importante', 'urgente', 'revisione'][array_rand(['importante', 'urgente', 'revisione'])]
                ],
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
                'updated_at' => Carbon::now()->subDays(rand(0, 15))
            ]);
        }

        $this->command->info('Creati 50 documenti di test con successo!');
    }
}
