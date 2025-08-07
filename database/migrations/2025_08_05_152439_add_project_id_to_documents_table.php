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
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('project_id')
                  ->nullable()
                  ->after('client_id')
                  ->constrained('projects')
                  ->onDelete('cascade');
            
            // Add index for better performance
            $table->index(['project_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropIndex(['project_id', 'status']);
            $table->dropColumn('project_id');
        });
    }
};
