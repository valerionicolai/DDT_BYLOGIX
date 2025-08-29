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
            
            // Foreign Keys (obbligatorie)
            $table->foreignId('document_id')
                  ->constrained('documents')
                  ->onDelete('cascade');
            
            $table->foreignId('material_type_id')
                  ->constrained('material_types')
                  ->onDelete('restrict');
            
            // Campi principali
            $table->text('description');
            $table->enum('state', ['da_conservare', 'da_trattenere', 'da_restituire'])
                  ->default('da_conservare');
            
            $table->date('due_date'); // Scadenza azione
            $table->string('qr_code', 255)->unique(); // QR Code univoco
            $table->text('notes')->nullable();
            
            // Metadati aggiuntivi
            $table->decimal('quantity', 10, 2)->default(1.00);
            $table->string('location')->nullable(); // Posizione fisica
            $table->json('metadata')->nullable(); // Dati aggiuntivi
            
            $table->timestamps();
            
            // Indici per performance
            $table->index(['qr_code']);
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
