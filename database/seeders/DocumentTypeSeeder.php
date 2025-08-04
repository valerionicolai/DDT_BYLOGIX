<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentTypes = [
            [
                'name' => 'Invoice',
                'description' => 'Commercial invoices and billing documents',
                'code' => 'INV',
                'color' => '#10B981',
                'icon' => 'heroicon-o-document-text',
                'sort_order' => 1,
            ],
            [
                'name' => 'Receipt',
                'description' => 'Payment receipts and proof of purchase',
                'code' => 'RCP',
                'color' => '#3B82F6',
                'icon' => 'heroicon-o-clipboard-document',
                'sort_order' => 2,
            ],
            [
                'name' => 'Contract',
                'description' => 'Legal contracts and agreements',
                'code' => 'CTR',
                'color' => '#8B5CF6',
                'icon' => 'heroicon-o-document-duplicate',
                'sort_order' => 3,
            ],
            [
                'name' => 'Purchase Order',
                'description' => 'Purchase orders and procurement documents',
                'code' => 'PO',
                'color' => '#F59E0B',
                'icon' => 'heroicon-o-clipboard-document-list',
                'sort_order' => 4,
            ],
            [
                'name' => 'Delivery Note',
                'description' => 'Delivery notes and shipping documents',
                'code' => 'DN',
                'color' => '#06B6D4',
                'icon' => 'heroicon-o-inbox',
                'sort_order' => 5,
            ],
            [
                'name' => 'Certificate',
                'description' => 'Certificates and official documents',
                'code' => 'CERT',
                'color' => '#EF4444',
                'icon' => 'heroicon-o-archive-box',
                'sort_order' => 6,
            ],
            [
                'name' => 'Report',
                'description' => 'Reports and analytical documents',
                'code' => 'RPT',
                'color' => '#84CC16',
                'icon' => 'heroicon-o-presentation-chart-line',
                'sort_order' => 7,
            ],
            [
                'name' => 'Specification',
                'description' => 'Technical specifications and requirements',
                'code' => 'SPEC',
                'color' => '#6366F1',
                'icon' => 'heroicon-o-code-bracket',
                'sort_order' => 8,
            ],
            [
                'name' => 'Image',
                'description' => 'Photos and image files',
                'code' => 'IMG',
                'color' => '#EC4899',
                'icon' => 'heroicon-o-photo',
                'sort_order' => 9,
            ],
            [
                'name' => 'Other',
                'description' => 'Miscellaneous documents',
                'code' => 'OTHER',
                'color' => '#6B7280',
                'icon' => 'heroicon-o-document',
                'sort_order' => 10,
            ],
        ];

        foreach ($documentTypes as $type) {
            DocumentType::firstOrCreate(
                ['code' => $type['code']],
                $type
            );
        }
    }
}