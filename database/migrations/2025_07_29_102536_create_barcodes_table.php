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
        Schema::create('barcodes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Codice barcode univoco
            $table->string('type')->default('CODE128'); // Tipo di barcode (CODE128, EAN13, QR, etc.)
            $table->string('format')->default('png'); // Formato immagine (png, svg, jpg)
            $table->text('data')->nullable(); // Dati codificati nel barcode
            $table->string('barcodeable_type'); // Tipo di entità associata (polymorphic)
            $table->unsignedBigInteger('barcodeable_id'); // ID dell'entità associata
            $table->boolean('is_active')->default(true); // Se il barcode è attivo
            $table->timestamp('generated_at')->useCurrent(); // Quando è stato generato
            $table->timestamp('expires_at')->nullable(); // Scadenza del barcode (opzionale)
            $table->json('metadata')->nullable(); // Metadati aggiuntivi (dimensioni, colori, etc.)
            $table->timestamps();
            
            // Indexes per performance
            $table->index(['barcodeable_type', 'barcodeable_id']);
            $table->index(['code']);
            $table->index(['is_active']);
            $table->index(['generated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barcodes');
    }
};
