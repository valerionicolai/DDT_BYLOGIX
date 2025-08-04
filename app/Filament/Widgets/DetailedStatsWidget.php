<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Project;
use App\Models\Client;
use App\Models\MaterialType;
use App\Models\Document;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class DetailedStatsWidget extends Widget
{
    protected static string $view = 'filament.widgets.detailed-stats';
    
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';
    
    public bool $isLoading = false;
    public bool $hasError = false;
    public string $errorMessage = '';
    public array $stats = [];

    public function mount(): void
    {
        $this->loadStats();
    }

    public function loadStats(): void
    {
        try {
            $this->isLoading = true;
            $this->hasError = false;
            
            $this->stats = Cache::remember('detailed_dashboard_stats', 300, function () {
                return $this->fetchDetailedStats();
            });
            
            $this->isLoading = false;
            
        } catch (Exception $e) {
            $this->isLoading = false;
            $this->hasError = true;
            $this->errorMessage = 'Failed to load detailed statistics. Please try again.';
            
            Log::error('Error fetching detailed dashboard stats: ' . $e->getMessage());
            
            // Emit error event
            $this->dispatch('stats-error', $this->errorMessage);
            
            $this->stats = [];
        }
    }

    private function fetchDetailedStats(): array
    {
        // Project Statistics
        $projectStats = [
            'total' => Project::count(),
            'active' => Project::where('status', 'active')->count(),
            'completed' => Project::where('status', 'completed')->count(),
            'draft' => Project::where('status', 'draft')->count(),
            'cancelled' => Project::where('status', 'cancelled')->count(),
            'on_hold' => Project::where('status', 'on_hold')->count(),
            'overdue' => Project::where('deadline', '<', now())
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count(),
            'this_month' => Project::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        // Client Statistics
        $clientStats = [
            'total' => Client::count(),
            'active' => Client::where('status', 'active')->count(),
            'inactive' => Client::where('status', 'inactive')->count(),
            'this_month' => Client::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        // Material Statistics
        $materialStats = [
            'total' => MaterialType::count(),
            'active' => MaterialType::where('status', 'active')->count(),
            'inactive' => MaterialType::where('status', 'inactive')->count(),
            'categories' => MaterialType::distinct('category')->count('category'),
        ];

        // Document Statistics
        $documentStats = [
            'total' => Document::count(),
            'draft' => Document::where('status', 'draft')->count(),
            'active' => Document::where('status', 'active')->count(),
            'archived' => Document::where('status', 'archived')->count(),
            'this_month' => Document::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        // Performance Metrics
        $performanceStats = [
            'completion_rate' => $projectStats['total'] > 0 
                ? round(($projectStats['completed'] / $projectStats['total']) * 100, 1)
                : 0,
            'active_rate' => $projectStats['total'] > 0 
                ? round(($projectStats['active'] / $projectStats['total']) * 100, 1)
                : 0,
            'client_engagement' => $clientStats['total'] > 0 
                ? round(($clientStats['active'] / $clientStats['total']) * 100, 1)
                : 0,
        ];

        return [
            'projects' => $projectStats,
            'clients' => $clientStats,
            'materials' => $materialStats,
            'documents' => $documentStats,
            'performance' => $performanceStats,
            'last_updated' => now()->format('Y-m-d H:i:s'),
        ];
    }

    public function refreshStats(): void
    {
        // Emit loading event
        $this->dispatch('stats-loading');
        
        // Clear cache
        Cache::forget('detailed_dashboard_stats');
        
        // Reset error state
        $this->hasError = false;
        $this->errorMessage = '';
        
        $this->loadStats();
        $this->dispatch('$refresh');
        
        // Emit update event after refresh
        $this->dispatch('stats-updated', [
            'significant_changes' => true,
            'timestamp' => now()->toISOString(),
            'widget' => 'detailed-stats'
        ]);
    }

    public function getPollingInterval(): ?string
    {
        return $this->hasError ? null : '60s'; // Refresh every minute
    }
}