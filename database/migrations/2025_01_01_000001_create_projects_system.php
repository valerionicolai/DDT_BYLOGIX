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
        // Projects Table
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // project manager/owner
            
            // Project state with extended enum values
            $table->enum('status', [
                'draft',
                'planning', 
                'active',
                'in_progress',
                'on_hold',
                'review',
                'completed',
                'cancelled',
                'archived'
            ])->default('draft');
            
            // Project priority with extended enum values  
            $table->enum('priority', ['low', 'medium', 'high', 'urgent', 'critical'])
                  ->default('medium');
            
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('deadline')->nullable();
            $table->decimal('budget', 12, 2)->nullable();
            $table->decimal('estimated_cost', 12, 2)->nullable();
            $table->decimal('actual_cost', 12, 2)->nullable();
            $table->integer('progress_percentage')->default(0); // 0-100
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Additional project-specific data
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['status']);
            $table->index(['priority']);
            $table->index(['deadline']);
            $table->index(['client_id', 'status']);
            $table->index(['user_id', 'status']);
        });

        // Documents Table (consolidated)
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path')->nullable(); // Made nullable per latest modification
            $table->string('barcode')->unique()->nullable();
            $table->timestamp('created_date')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
            $table->json('metadata')->nullable();
            
            // Foreign key relationships
            $table->foreignId('document_type_id')->nullable()->constrained('document_types')->onDelete('set null');
            $table->foreignId('document_category_id')->nullable()->constrained('document_categories')->onDelete('set null');
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null');
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade');
            
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
            $table->index(['project_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
        Schema::dropIfExists('projects');
    }
};