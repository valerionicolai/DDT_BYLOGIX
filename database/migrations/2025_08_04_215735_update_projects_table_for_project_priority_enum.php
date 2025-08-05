<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update existing priority data to match new enum values
        DB::statement("UPDATE projects SET priority = 'low' WHERE priority IN ('low', 'Low', 'LOW')");
        DB::statement("UPDATE projects SET priority = 'medium' WHERE priority IN ('medium', 'Medium', 'MEDIUM', 'normal', 'Normal', 'NORMAL')");
        DB::statement("UPDATE projects SET priority = 'high' WHERE priority IN ('high', 'High', 'HIGH')");
        DB::statement("UPDATE projects SET priority = 'urgent' WHERE priority IN ('urgent', 'Urgent', 'URGENT')");
        DB::statement("UPDATE projects SET priority = 'critical' WHERE priority IN ('critical', 'Critical', 'CRITICAL')");
        
        // Set any other priority values to 'medium' as default
        DB::statement("UPDATE projects SET priority = 'medium' WHERE priority NOT IN ('low', 'medium', 'high', 'urgent', 'critical')");
        
        // Now change the column type to enum
        Schema::table('projects', function (Blueprint $table) {
            $table->enum('priority', ['low', 'medium', 'high', 'urgent', 'critical'])
                  ->default('medium')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('priority')->default('medium')->change();
        });
    }
};
