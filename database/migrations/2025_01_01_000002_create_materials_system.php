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
            
            // Foreign Keys
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('material_type_id')->constrained('material_types')->onDelete('restrict');
            
            // Main fields
            $table->text('description');
            $table->enum('state', ['da_conservare', 'da_trattenere', 'da_restituire'])->default('da_conservare');
            $table->date('due_date');
            $table->string('barcode', 255)->unique(); // already using barcode name
            $table->text('notes')->nullable();
            
            // Additional metadata
            $table->decimal('quantity', 10, 2)->default(1.00);
            $table->string('location')->nullable();
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['barcode']);
            $table->index(['state', 'due_date']);
            $table->index(['document_id', 'state']);
            $table->index(['material_type_id']);
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