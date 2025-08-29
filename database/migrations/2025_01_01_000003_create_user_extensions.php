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
        // Modify users table to add role and is_active
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'user', 'manager', 'viewer'])->default('user');
            $table->boolean('is_active')->default(true);
            
            // Add indexes for performance
            $table->index(['role']);
            $table->index(['is_active']);
        });
        
        // Create Personal Access Tokens table
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            // L'indice su tokenable_type e tokenable_id è già creato da morphs(), evitiamo duplicati.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['is_active']);
            $table->dropColumn(['role', 'is_active']);
        });
    }
};