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
        Schema::table('materials', function (Blueprint $table) {
            // Rinomina la colonna qr_code in barcode
            $table->renameColumn('qr_code', 'barcode');
            
            // Aggiorna l'indice
            $table->dropIndex(['qr_code']);
            $table->index(['barcode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            // Ripristina la colonna barcode in qr_code
            $table->renameColumn('barcode', 'qr_code');
            
            // Ripristina l'indice
            $table->dropIndex(['barcode']);
            $table->index(['qr_code']);
        });
    }
};
