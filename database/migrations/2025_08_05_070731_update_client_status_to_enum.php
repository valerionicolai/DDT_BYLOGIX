<?php

use App\Enums\ClientStatus;
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
        // Update existing client status values to match enum values
        $statusMappings = [
            'active' => ClientStatus::ACTIVE->value,
            'attivo' => ClientStatus::ACTIVE->value,
            'inactive' => ClientStatus::INACTIVE->value,
            'inattivo' => ClientStatus::INACTIVE->value,
            'prospect' => ClientStatus::PROSPECT->value,
            'prospetto' => ClientStatus::PROSPECT->value,
            'suspended' => ClientStatus::SUSPENDED->value,
            'sospeso' => ClientStatus::SUSPENDED->value,
            'archived' => ClientStatus::ARCHIVED->value,
            'archiviato' => ClientStatus::ARCHIVED->value,
        ];

        foreach ($statusMappings as $oldStatus => $newStatus) {
            DB::table('clients')
                ->where('status', $oldStatus)
                ->update(['status' => $newStatus]);
        }

        // Set any remaining null or empty status to prospect
        DB::table('clients')
            ->whereNull('status')
            ->orWhere('status', '')
            ->update(['status' => ClientStatus::PROSPECT->value]);

        // Set any unrecognized status values to prospect
        $validStatuses = array_column(ClientStatus::cases(), 'value');
        DB::table('clients')
            ->whereNotIn('status', $validStatuses)
            ->update(['status' => ClientStatus::PROSPECT->value]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert enum values back to simple strings
        $reverseMappings = [
            ClientStatus::ACTIVE->value => 'active',
            ClientStatus::INACTIVE->value => 'inactive',
            ClientStatus::PROSPECT->value => 'prospect',
            ClientStatus::SUSPENDED->value => 'suspended',
            ClientStatus::ARCHIVED->value => 'archived',
        ];

        foreach ($reverseMappings as $enumValue => $stringValue) {
            DB::table('clients')
                ->where('status', $enumValue)
                ->update(['status' => $stringValue]);
        }
    }
};
