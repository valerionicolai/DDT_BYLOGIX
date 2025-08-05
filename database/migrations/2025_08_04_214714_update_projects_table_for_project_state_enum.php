<?php

use App\Enums\ProjectState;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // First, update existing data to match new enum values
            DB::statement("UPDATE projects SET status = 'draft' WHERE status = 'draft'");
            DB::statement("UPDATE projects SET status = 'active' WHERE status = 'active'");
            DB::statement("UPDATE projects SET status = 'completed' WHERE status = 'completed'");
            DB::statement("UPDATE projects SET status = 'cancelled' WHERE status = 'cancelled'");
            DB::statement("UPDATE projects SET status = 'on_hold' WHERE status = 'on_hold'");
            
            // Update any other status values to draft as default
            DB::statement("UPDATE projects SET status = 'draft' WHERE status NOT IN ('draft', 'planning', 'active', 'in_progress', 'on_hold', 'review', 'completed', 'cancelled', 'archived')");
            
            // Modify the column to use enum
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
            ])->default('draft')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Revert back to string column
            $table->string('status')->default('draft')->change();
        });
    }
};
