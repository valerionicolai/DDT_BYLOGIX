<?php

namespace Database\Factories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        $categories = ['Contract', 'Invoice', 'Receipt', 'Certificate', 'Manual', 'Purchase Order'];
        $statuses = ['draft', 'active', 'archived'];
        $suppliers = [
            'ABC Company',
            'XYZ Corp',
            'Tech Solutions Ltd',
            'Global Industries',
            'Innovation Partners',
            'Quality Services Inc'
        ];

        $category = $this->faker->randomElement($categories);
        $createdDate = $this->faker->dateTimeBetween('-1 year', 'now');
        
        return [
            'title' => 'Document ' . $category . ' #' . $this->faker->unique()->numberBetween(1, 9999),
            'description' => $this->faker->paragraph(2),
            'category' => $category,
            'supplier' => $this->faker->randomElement($suppliers),
            'file_path' => 'documents/' . $this->faker->uuid() . '.pdf',
            'file_name' => $this->faker->word() . '.pdf',
            'file_size' => $this->faker->numberBetween(1024, 5242880), // 1KB to 5MB
            'file_type' => 'application/pdf',
            'status' => $this->faker->randomElement($statuses),
            'barcode' => strtoupper(substr($category, 0, 1)) . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'metadata' => [
                'version' => '1.0',
                'category' => $category,
                'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
                'tags' => $this->faker->randomElements(['urgent', 'important', 'review', 'approved'], $this->faker->numberBetween(0, 2))
            ],
            'created_date' => $createdDate,
            'due_date' => $this->faker->optional(0.7)->dateTimeBetween($createdDate, '+6 months'),
            'created_at' => $createdDate,
            'updated_at' => $this->faker->dateTimeBetween($createdDate, 'now')
        ];
    }

    /**
     * Indicate that the document is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Indicate that the document is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the document is archived.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'archived',
        ]);
    }

    /**
     * Indicate that the document is overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'due_date' => $this->faker->dateTimeBetween('-1 month', '-1 day'),
        ]);
    }

    /**
     * Indicate that the document is of a specific category.
     */
    public function category(string $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => $category,
            'barcode' => strtoupper(substr($category, 0, 1)) . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
        ]);
    }

    /**
     * Indicate that the document has no file.
     */
    public function withoutFile(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_path' => null,
            'file_name' => null,
            'file_size' => null,
            'file_type' => null,
        ]);
    }

    /**
     * Indicate that the document has a specific barcode.
     */
    public function withBarcode(string $barcode): static
    {
        return $this->state(fn (array $attributes) => [
            'barcode' => $barcode,
        ]);
    }
}