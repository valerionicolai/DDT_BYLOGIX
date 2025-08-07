<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\MaterialType;
use App\Models\Document;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assicuriamoci che esistano i tipi di materiali e documenti
        $materialTypes = MaterialType::all();
        $documents = Document::all();

        if ($materialTypes->isEmpty()) {
            $this->command->warn('Assicurati che esistano MaterialTypes prima di eseguire questo seeder.');
            return;
        }

        $materials = [
              [
                  'description' => 'Cemento Portland 42.5R - Sacco 25kg',
                  'state' => 'da_conservare',
                  'quantity' => 100,
                  'location' => 'Magazzino A - Scaffale 1',
                  'notes' => 'Lotto produzione: 2024-001',
                  'due_date' => now()->addDays(30),
              ],
              [
                  'description' => 'Tondino acciaio FeB44k Ø12mm - Barra 12m',
                  'state' => 'da_conservare',
                  'quantity' => 50,
                  'location' => 'Deposito esterno - Area B',
                  'notes' => 'Certificato qualità incluso',
                  'due_date' => now()->addDays(60),
              ],
              [
                  'description' => 'Mattoni forati 25x12x8 cm',
                  'state' => 'da_conservare',
                  'quantity' => 1000,
                  'location' => 'Magazzino B - Bancale 3',
                  'notes' => 'Classe di resistenza M5',
                  'due_date' => now()->addDays(45),
              ],
              [
                  'description' => 'Sabbia fine lavata - Metro cubo',
                  'state' => 'da_trattenere',
                  'quantity' => 15,
                  'location' => 'Deposito esterno - Area C',
                  'notes' => 'Riservata per progetto Villa Rossi',
                  'due_date' => now()->addDays(7),
              ],
              [
                  'description' => 'Ghiaia 15-25mm - Metro cubo',
                  'state' => 'da_conservare',
                  'quantity' => 25,
                  'location' => 'Deposito esterno - Area C',
                  'notes' => 'Materiale lavato e vagliato',
                  'due_date' => now()->addDays(90),
              ],
              [
                  'description' => 'Pannelli isolanti XPS 100mm',
                  'state' => 'da_trattenere',
                  'quantity' => 30,
                  'location' => 'Cantiere Villa Bianchi',
                  'notes' => 'In uso per isolamento termico',
                  'due_date' => now()->addDays(14),
              ],
              [
                  'description' => 'Guaina bituminosa 4mm',
                  'state' => 'da_conservare',
                  'quantity' => 20,
                  'location' => 'Magazzino A - Scaffale 5',
                  'notes' => 'Rotoli da 10 metri',
                  'due_date' => now()->addDays(120),
              ],
              [
                  'description' => 'Calcestruzzo C25/30 - Metro cubo',
                  'state' => 'da_restituire',
                  'quantity' => 50,
                  'location' => 'In arrivo',
                  'notes' => 'Consegna prevista per domani',
                  'due_date' => now()->addDay(),
              ],
          ];

        foreach ($materials as $materialData) {
            Material::create([
                'description' => $materialData['description'],
                'material_type_id' => $materialTypes->random()->id,
                'document_id' => $documents->isNotEmpty() ? $documents->random()->id : null,
                'state' => $materialData['state'],
                'quantity' => $materialData['quantity'],
                'location' => $materialData['location'],
                'notes' => $materialData['notes'],
                'due_date' => $materialData['due_date'] ?? null,
                'metadata' => [
                    'source' => 'seeder',
                    'category' => ['construction', 'insulation', 'structural'][array_rand(['construction', 'insulation', 'structural'])],
                ],
            ]);
        }

        $this->command->info('Creati ' . count($materials) . ' materiali di esempio con barcode automatici.');
    }
}
