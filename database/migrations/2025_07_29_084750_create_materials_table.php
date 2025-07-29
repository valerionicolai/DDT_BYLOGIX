<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entry_document_id')->constrained()->onDelete('cascade');
            $table->foreignId('material_type_id')->constrained()->onDelete('cascade');
            $table->string('description'); // Specific description for this material instance
            $table->decimal('quantity', 10, 3); // Quantity with 3 decimal places for precision
            $table->string('unit_of_measure'); // Can override material_type unit
            $table->decimal('unit_price', 10, 2); // Price per unit for this specific purchase
            $table->decimal('total_price', 10, 2); // Total price (quantity * unit_price)
            $table->decimal('vat_rate', 5, 2)->default(22.00); // VAT rate percentage
            $table->decimal('vat_amount', 10, 2)->default(0); // VAT amount
            $table->string('lot_number')->nullable(); // Batch/lot tracking
            $table->date('expiry_date')->nullable(); // For materials with expiration
            $table->string('location')->nullable(); // Storage location
            $table->enum('status', ['ordered', 'received', 'used', 'returned'])->default('received');
            $table->json('properties')->nullable(); // Additional material properties
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['entry_document_id']);
            $table->index(['material_type_id']);
            $table->index(['status']);
            $table->index(['lot_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
