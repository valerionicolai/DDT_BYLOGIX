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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('document_type'); // tipo di documento (fattura, contratto, etc.)
            $table->string('supplier_name')->nullable(); // nome fornitore
            $table->string('file_path')->nullable(); // percorso del file
            $table->string('file_name')->nullable(); // nome originale del file
            $table->string('file_size')->nullable(); // dimensione del file
            $table->string('mime_type')->nullable(); // tipo MIME del file
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // utente che ha creato il documento
            $table->enum('status', ['draft', 'active', 'archived', 'deleted'])->default('active');
            $table->date('document_date')->nullable(); // data del documento
            $table->decimal('amount', 12, 2)->nullable(); // importo se applicabile
            $table->string('reference_number')->nullable(); // numero di riferimento
            $table->json('metadata')->nullable(); // dati aggiuntivi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
