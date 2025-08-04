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
            $table->string('file_path');
            $table->string('barcode')->unique()->nullable();
            $table->timestamp('created_date')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
            $table->json('metadata')->nullable();
            
            // Foreign key relationships
            $table->foreignId('document_type_id')->nullable()->constrained('document_types')->onDelete('set null');
            $table->foreignId('document_category_id')->nullable()->constrained('document_categories')->onDelete('set null');
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better performance
            $table->index(['title']);
            $table->index(['status']);
            $table->index(['created_date']);
            $table->index(['due_date']);
            $table->index(['document_type_id']);
            $table->index(['document_category_id']);
            $table->index(['client_id']);
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
