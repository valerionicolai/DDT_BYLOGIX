<?php

use App\Enums\ClientStatus;
use App\Enums\ClientType;
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
        // Document Types Table
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->string('code', 20)->unique();
            $table->string('color', 7)->default('#6B7280'); // Hex color
            $table->string('icon', 50)->default('heroicon-o-document');
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        // Document Categories Table
        Schema::create('document_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->string('code', 50)->unique();
            $table->string('color', 7)->default('#6B7280');
            $table->string('icon', 50)->default('heroicon-o-folder');
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        // Material Types Table
        Schema::create('material_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->string('unit_of_measure', 50)->nullable();
            $table->decimal('default_price', 10, 2)->nullable();
            $table->string('category', 50)->nullable();
            $table->json('properties')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index(['status', 'category']);
        });

        // Clients Table (consolidated with all modifications)
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('vat_number')->nullable();
            $table->enum('type', ['cliente', 'fornitore'])->default('cliente');
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'inactive', 'prospect', 'suspended', 'archived'])->default('active');
            $table->timestamps();
        });

        // Client Contacts Table
        Schema::create('client_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('area_role')->nullable(); // allineato a seeder e model
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['client_id', 'is_primary']);
            $table->index(['client_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_contacts');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('material_types');
        Schema::dropIfExists('document_categories');
        Schema::dropIfExists('document_types');
    }
};