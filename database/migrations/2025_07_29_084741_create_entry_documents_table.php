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
        Schema::create('entry_documents', function (Blueprint $table) {
            $table->id();
            $table->string('document_number')->unique();
            $table->string('document_type')->default('entry'); // entry, delivery, invoice, etc.
            $table->string('supplier_name');
            $table->string('supplier_vat')->nullable();
            $table->string('supplier_address')->nullable();
            $table->date('document_date');
            $table->date('delivery_date')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('vat_amount', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2)->default(0);
            $table->string('currency', 3)->default('EUR');
            $table->enum('status', ['draft', 'confirmed', 'received', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Additional flexible data
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User who created the document
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['document_date', 'status']);
            $table->index(['supplier_name']);
            $table->index(['project_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entry_documents');
    }
};
