<?php

namespace App\Services;

use App\Enums\ClientStatus;
use App\Models\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ClientStatusService
{
    /**
     * Transition a client to a new status.
     */
    public function transitionClient(Client $client, ClientStatus $newStatus, ?string $reason = null): bool
    {
        $currentStatus = $client->status;
        
        // Check if transition is valid
        if (!$this->canTransition($currentStatus, $newStatus)) {
            return false;
        }
        
        // Perform the transition
        $client->status = $newStatus;
        $client->save();
        
        // Log the transition (you can implement logging here)
        $this->logStatusTransition($client, $currentStatus, $newStatus, $reason);
        
        return true;
    }
    
    /**
     * Check if a status transition is valid.
     */
    public function canTransition(ClientStatus $from, ClientStatus $to): bool
    {
        return in_array($to, $from->getValidTransitionsTo());
    }
    
    /**
     * Get valid transitions for a client's current status.
     */
    public function getValidTransitions(Client $client): array
    {
        return $client->status->getValidTransitionsTo();
    }
    
    /**
     * Get clients by status.
     */
    public function getClientsByStatus(ClientStatus $status): Collection
    {
        return Client::where('status', $status)->get();
    }
    
    /**
     * Get status statistics.
     */
    public function getStatusStatistics(): array
    {
        $total = Client::count();
        
        return [
            'total' => $total,
            'active' => Client::where('status', ClientStatus::ACTIVE)->count(),
            'inactive' => Client::where('status', ClientStatus::INACTIVE)->count(),
            'prospects' => Client::where('status', ClientStatus::PROSPECT)->count(),
            'suspended' => Client::where('status', ClientStatus::SUSPENDED)->count(),
            'archived' => Client::where('status', ClientStatus::ARCHIVED)->count(),
            'business_ready' => Client::whereIn('status', [
                ClientStatus::ACTIVE,
                ClientStatus::PROSPECT
            ])->count(),
        ];
    }
    
    /**
     * Archive inactive clients that meet certain criteria.
     */
    public function archiveInactiveClients(int $daysInactive = 365): int
    {
        $cutoffDate = now()->subDays($daysInactive);
        
        $clientsToArchive = Client::where('status', ClientStatus::INACTIVE)
            ->where('updated_at', '<', $cutoffDate)
            ->whereDoesntHave('projects', function ($query) use ($cutoffDate) {
                $query->where('created_at', '>', $cutoffDate);
            })
            ->get();
        
        $count = 0;
        foreach ($clientsToArchive as $client) {
            if ($this->transitionClient($client, ClientStatus::ARCHIVED, 'Auto-archived due to inactivity')) {
                $count++;
            }
        }
        
        return $count;
    }
    
    /**
     * Activate prospect clients that have projects.
     */
    public function activateProspectsWithProjects(): int
    {
        $prospectsWithProjects = Client::where('status', ClientStatus::PROSPECT)
            ->whereHas('projects')
            ->get();
        
        $count = 0;
        foreach ($prospectsWithProjects as $client) {
            if ($this->transitionClient($client, ClientStatus::ACTIVE, 'Auto-activated due to project creation')) {
                $count++;
            }
        }
        
        return $count;
    }
    
    /**
     * Log status transition (implement as needed).
     */
    private function logStatusTransition(Client $client, ClientStatus $from, ClientStatus $to, ?string $reason): void
    {
        // You can implement logging to database, file, or external service here
        Log::info("Client status transition", [
            'client_id' => $client->id,
            'client_name' => $client->name,
            'from_status' => $from->value,
            'to_status' => $to->value,
            'reason' => $reason,
            'timestamp' => now(),
        ]);
    }
}