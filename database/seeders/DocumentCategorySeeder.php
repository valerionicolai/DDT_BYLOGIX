<?php

namespace Database\Seeders;

use App\Models\DocumentCategory;
use Illuminate\Database\Seeder;

class DocumentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Financial',
                'description' => 'Financial documents including invoices, receipts, and payment records',
                'code' => 'financial',
                'color' => '#10b981',
                'icon' => 'heroicon-o-currency-dollar',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Legal',
                'description' => 'Legal documents including contracts, agreements, and compliance documents',
                'code' => 'legal',
                'color' => '#3b82f6',
                'icon' => 'heroicon-o-scale',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Administrative',
                'description' => 'Administrative documents including forms, applications, and correspondence',
                'code' => 'administrative',
                'color' => '#8b5cf6',
                'icon' => 'heroicon-o-clipboard-document',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Technical',
                'description' => 'Technical documents including specifications, manuals, and reports',
                'code' => 'technical',
                'color' => '#f59e0b',
                'icon' => 'heroicon-o-cog-6-tooth',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Marketing',
                'description' => 'Marketing materials including brochures, presentations, and promotional content',
                'code' => 'marketing',
                'color' => '#ef4444',
                'icon' => 'heroicon-o-megaphone',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Human Resources',
                'description' => 'HR documents including employee records, policies, and training materials',
                'code' => 'human-resources',
                'color' => '#06b6d4',
                'icon' => 'heroicon-o-users',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Operations',
                'description' => 'Operational documents including procedures, workflows, and process documentation',
                'code' => 'operations',
                'color' => '#84cc16',
                'icon' => 'heroicon-o-cog',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Archive',
                'description' => 'Archived documents that are no longer actively used but need to be retained',
                'code' => 'archive',
                'color' => '#6b7280',
                'icon' => 'heroicon-o-archive-box',
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'name' => 'Personal',
                'description' => 'Personal documents and private files',
                'code' => 'personal',
                'color' => '#ec4899',
                'icon' => 'heroicon-o-user',
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'name' => 'Other',
                'description' => 'Miscellaneous documents that don\'t fit into other categories',
                'code' => 'other',
                'color' => '#64748b',
                'icon' => 'heroicon-o-folder',
                'is_active' => true,
                'sort_order' => 10,
            ],
        ];

        foreach ($categories as $category) {
            DocumentCategory::create($category);
        }
    }
}