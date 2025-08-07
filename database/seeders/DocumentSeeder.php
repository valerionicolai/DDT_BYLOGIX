<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\DocumentCategory;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assicuriamoci che esistano i tipi e categorie necessari
        $documentTypes = DocumentType::all();
        $documentCategories = DocumentCategory::all();
        $clients = Client::all();
        $projects = Project::all();

        if ($documentTypes->isEmpty() || $documentCategories->isEmpty() || $clients->isEmpty()) {
            $this->command->warn('Assicurati che esistano DocumentTypes, DocumentCategories e Clients prima di eseguire questo seeder.');
            return;
        }

        $documents = [
            [
                'title' => 'Fattura Fornitore ABC - Gennaio 2024',
                'description' => 'Fattura per materiali edili forniti nel mese di gennaio',
                'status' => 'active',
                'created_date' => now()->subDays(30),
                'due_date' => now()->addDays(30),
            ],
            [
                'title' => 'DDT Consegna Materiali - Progetto Villa',
                'description' => 'Documento di trasporto per consegna materiali presso cantiere villa',
                'status' => 'active',
                'created_date' => now()->subDays(15),
                'due_date' => now()->addDays(15),
            ],
            [
                'title' => 'Certificato Qualità Cemento',
                'description' => 'Certificato di qualità per lotto cemento Portland',
                'status' => 'active',
                'created_date' => now()->subDays(10),
                'due_date' => now()->addDays(60),
            ],
            [
                'title' => 'Contratto Fornitura Acciaio',
                'description' => 'Contratto annuale per fornitura tondini di acciaio',
                'status' => 'active',
                'created_date' => now()->subDays(60),
                'due_date' => now()->addDays(300),
            ],
            [
                'title' => 'Scheda Sicurezza Prodotti Chimici',
                'description' => 'Schede di sicurezza per additivi e prodotti chimici',
                'status' => 'active',
                'created_date' => now()->subDays(5),
                'due_date' => now()->addDays(365),
            ],
        ];

        foreach ($documents as $documentData) {
             Document::create([
                 'title' => $documentData['title'],
                 'description' => $documentData['description'],
                 'file_path' => 'documents/sample_' . uniqid() . '.pdf', // Placeholder file path
                 'document_type_id' => $documentTypes->random()->id,
                 'document_category_id' => $documentCategories->random()->id,
                 'client_id' => $clients->random()->id,
                 'project_id' => $projects->isNotEmpty() ? $projects->random()->id : null,
                 'status' => $documentData['status'],
                 'created_date' => $documentData['created_date'],
                 'due_date' => $documentData['due_date'],
                 'metadata' => [
                     'source' => 'seeder',
                     'priority' => ['high', 'medium', 'low'][array_rand(['high', 'medium', 'low'])],
                 ],
             ]);
         }

        $this->command->info('Creati ' . count($documents) . ' documenti di esempio con barcode automatici.');
    }
}
