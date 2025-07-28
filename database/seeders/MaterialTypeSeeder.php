<?php

namespace Database\Seeders;

use App\Models\MaterialType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaterialTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materialTypes = [
            [
                'name' => 'Legno di Rovere',
                'description' => 'Legno massello di rovere europeo, ideale per mobili di alta qualità e pavimentazioni',
                'unit_of_measure' => 'm³',
                'default_price' => 850.00,
                'category' => 'Legno',
                'properties' => [
                    'densità' => '750 kg/m³',
                    'durezza' => 'Alta',
                    'resistenza_umidità' => 'Media',
                    'colore' => 'Marrone chiaro',
                    'venatura' => 'Pronunciata'
                ],
                'status' => 'active',
            ],
            [
                'name' => 'Acciaio Inox 316L',
                'description' => 'Acciaio inossidabile austenico con basso contenuto di carbonio, resistente alla corrosione',
                'unit_of_measure' => 'kg',
                'default_price' => 12.50,
                'category' => 'Metallo',
                'properties' => [
                    'composizione' => 'Fe, Cr 17-20%, Ni 10-14%, Mo 2-3%',
                    'resistenza_corrosione' => 'Eccellente',
                    'temperatura_max' => '800°C',
                    'magnetico' => 'No'
                ],
                'status' => 'active',
            ],
            [
                'name' => 'Cemento Portland 42.5',
                'description' => 'Cemento Portland di classe 42.5 per costruzioni strutturali',
                'unit_of_measure' => 'ton',
                'default_price' => 120.00,
                'category' => 'Cemento',
                'properties' => [
                    'resistenza_28gg' => '42.5 MPa',
                    'tempo_presa' => '2-4 ore',
                    'colore' => 'Grigio',
                    'finezza' => '3500 cm²/g'
                ],
                'status' => 'active',
            ],
            [
                'name' => 'Vetro Temperato 8mm',
                'description' => 'Vetro temperato di sicurezza spessore 8mm per applicazioni strutturali',
                'unit_of_measure' => 'm²',
                'default_price' => 45.00,
                'category' => 'Vetro',
                'properties' => [
                    'spessore' => '8mm',
                    'resistenza' => 'Temperato',
                    'trasparenza' => '90%',
                    'peso_specifico' => '20 kg/m²'
                ],
                'status' => 'active',
            ],
            [
                'name' => 'Plastica PVC Rigido',
                'description' => 'Policloruro di vinile rigido per tubazioni e profilati',
                'unit_of_measure' => 'kg',
                'default_price' => 2.80,
                'category' => 'Plastica',
                'properties' => [
                    'densità' => '1.4 g/cm³',
                    'temperatura_max' => '60°C',
                    'resistenza_chimica' => 'Buona',
                    'colore' => 'Bianco/Grigio'
                ],
                'status' => 'active',
            ],
            [
                'name' => 'Alluminio 6061-T6',
                'description' => 'Lega di alluminio strutturale con trattamento termico T6',
                'unit_of_measure' => 'kg',
                'default_price' => 4.20,
                'category' => 'Metallo',
                'properties' => [
                    'resistenza_snervamento' => '276 MPa',
                    'densità' => '2.7 g/cm³',
                    'conducibilità_termica' => '167 W/m·K',
                    'resistenza_corrosione' => 'Buona'
                ],
                'status' => 'active',
            ],
            [
                'name' => 'Mattone Refrattario',
                'description' => 'Mattone refrattario per forni e camini, resistente alle alte temperature',
                'unit_of_measure' => 'pz',
                'default_price' => 3.50,
                'category' => 'Ceramica',
                'properties' => [
                    'temperatura_max' => '1400°C',
                    'densità' => '2.3 g/cm³',
                    'porosità' => '18%',
                    'dimensioni' => '230x114x65mm'
                ],
                'status' => 'active',
            ],
            [
                'name' => 'Fibra di Carbonio',
                'description' => 'Tessuto in fibra di carbonio per rinforzi strutturali',
                'unit_of_measure' => 'm²',
                'default_price' => 85.00,
                'category' => 'Composito',
                'properties' => [
                    'peso' => '200 g/m²',
                    'resistenza_trazione' => '3500 MPa',
                    'modulo_elastico' => '230 GPa',
                    'spessore' => '0.2mm'
                ],
                'status' => 'active',
            ],
            [
                'name' => 'Isolante Poliuretano',
                'description' => 'Pannello isolante in poliuretano espanso per isolamento termico',
                'unit_of_measure' => 'm²',
                'default_price' => 18.50,
                'category' => 'Isolante',
                'properties' => [
                    'conducibilità_termica' => '0.023 W/m·K',
                    'spessore' => '50mm',
                    'densità' => '35 kg/m³',
                    'resistenza_compressione' => '150 kPa'
                ],
                'status' => 'active',
            ],
            [
                'name' => 'Rame Elettrolitico',
                'description' => 'Rame puro al 99.9% per applicazioni elettriche',
                'unit_of_measure' => 'kg',
                'default_price' => 8.90,
                'category' => 'Metallo',
                'properties' => [
                    'purezza' => '99.9%',
                    'conducibilità_elettrica' => '58.5 MS/m',
                    'densità' => '8.96 g/cm³',
                    'temperatura_fusione' => '1085°C'
                ],
                'status' => 'active',
            ],
            [
                'name' => 'Legno di Pino Siberiano',
                'description' => 'Legno di pino siberiano per strutture e carpenteria',
                'unit_of_measure' => 'm³',
                'default_price' => 420.00,
                'category' => 'Legno',
                'properties' => [
                    'densità' => '450 kg/m³',
                    'umidità' => '12%',
                    'classe_resistenza' => 'C24',
                    'origine' => 'Siberia'
                ],
                'status' => 'active',
            ],
            [
                'name' => 'Gesso Ceramico',
                'description' => 'Gesso ceramico per finiture interne e decorazioni',
                'unit_of_measure' => 'kg',
                'default_price' => 1.20,
                'category' => 'Gesso',
                'properties' => [
                    'tempo_presa' => '15-20 min',
                    'resistenza_compressione' => '8 MPa',
                    'colore' => 'Bianco',
                    'granulometria' => 'Fine'
                ],
                'status' => 'active',
            ],
            [
                'name' => 'Titanio Grade 2',
                'description' => 'Titanio commercialmente puro per applicazioni aerospaziali',
                'unit_of_measure' => 'kg',
                'default_price' => 45.00,
                'category' => 'Metallo',
                'properties' => [
                    'purezza' => '99.2%',
                    'resistenza_trazione' => '345 MPa',
                    'densità' => '4.51 g/cm³',
                    'resistenza_corrosione' => 'Eccellente'
                ],
                'status' => 'inactive',
            ],
            [
                'name' => 'Gomma EPDM',
                'description' => 'Gomma etilene-propilene per guarnizioni e membrane impermeabili',
                'unit_of_measure' => 'm²',
                'default_price' => 12.00,
                'category' => 'Gomma',
                'properties' => [
                    'temperatura_esercizio' => '-40°C / +120°C',
                    'resistenza_ozono' => 'Eccellente',
                    'spessore' => '2mm',
                    'durezza' => '60 Shore A'
                ],
                'status' => 'active',
            ],
            [
                'name' => 'Pietra Naturale Travertino',
                'description' => 'Travertino naturale per rivestimenti e pavimentazioni',
                'unit_of_measure' => 'm²',
                'default_price' => 35.00,
                'category' => 'Pietra',
                'properties' => [
                    'origine' => 'Tivoli',
                    'porosità' => 'Media',
                    'resistenza_gelo' => 'Buona',
                    'finitura' => 'Levigata'
                ],
                'status' => 'active',
            ]
        ];

        foreach ($materialTypes as $materialType) {
            MaterialType::create($materialType);
        }

        $this->command->info('MaterialTypeSeeder completato: ' . count($materialTypes) . ' tipi di materiale creati.');
    }
}
