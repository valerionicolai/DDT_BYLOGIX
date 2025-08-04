<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Project;
use App\Models\Client;
use App\Models\MaterialType;
use App\Models\Document;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected int | string | array $columnSpan = 'full';
    
    public bool $isLoading = false;
    public bool $hasError = false;
    public string $errorMessage = '';

    protected function getStats(): array
    {
        try {
            $this->isLoading = true;
            $this->hasError = false;
            
            // Cache the stats for 5 minutes to improve performance
            $stats = Cache::remember('dashboard_stats', 300, function () {
                return $this->fetchStatsData();
            });
            
            $this->isLoading = false;
            return $stats;
            
        } catch (Exception $e) {
            $this->isLoading = false;
            $this->hasError = true;
            $this->errorMessage = 'Failed to load statistics. Please try refreshing.';
            
            Log::error('Dashboard stats error: ' . $e->getMessage());
            
            // Emit error event
            $this->dispatch('stats-error', $this->errorMessage);
            
            return $this->getErrorStats();
        }
    }
    
    private function fetchStatsData(): array
    {
        $totalProjects = Project::count();
        $activeProjects = Project::where('status', 'active')->count();
        $completedProjects = Project::where('status', 'completed')->count();
        $totalClients = Client::count();
        $activeClients = Client::where('status', 'active')->count();
        $totalMaterials = MaterialType::count();
        $activeMaterials = MaterialType::where('status', 'active')->count();
        $totalDocuments = Document::count();
            
        // Calculate growth percentages (mock data for now)
        $projectGrowth = $this->calculateGrowth('projects', $totalProjects);
        $clientGrowth = $this->calculateGrowth('clients', $totalClients);
        $materialGrowth = $this->calculateGrowth('materials', $totalMaterials);
        $documentGrowth = $this->calculateGrowth('documents', $totalDocuments);

        return [
            Stat::make('Total Projects', $totalProjects)
                ->description($projectGrowth['description'])
                ->descriptionIcon($projectGrowth['icon'])
                ->color($projectGrowth['color'])
                ->chart($projectGrowth['chart'])
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors',
                    'wire:click' => '$dispatch("navigate-to", { route: "projects" })',
                ]),

            Stat::make('Active Projects', $activeProjects)
                ->description('Currently in progress')
                ->descriptionIcon('heroicon-m-play')
                ->color('success')
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors',
                ]),

            Stat::make('Completed Projects', $completedProjects)
                ->description('Successfully finished')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors',
                ]),

            Stat::make('Total Clients', $totalClients)
                ->description($clientGrowth['description'])
                ->descriptionIcon($clientGrowth['icon'])
                ->color($clientGrowth['color'])
                ->chart($clientGrowth['chart'])
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors',
                    'wire:click' => '$dispatch("navigate-to", { route: "clients" })',
                ]),

            Stat::make('Active Clients', $activeClients)
                ->description('Currently engaged')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors',
                ]),

            Stat::make('Material Types', $totalMaterials)
                ->description($materialGrowth['description'])
                ->descriptionIcon($materialGrowth['icon'])
                ->color($materialGrowth['color'])
                ->chart($materialGrowth['chart'])
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors',
                    'wire:click' => '$dispatch("navigate-to", { route: "material-types" })',
                ]),

            Stat::make('Documents', $totalDocuments)
                ->description($documentGrowth['description'])
                ->descriptionIcon($documentGrowth['icon'])
                ->color($documentGrowth['color'])
                ->chart($documentGrowth['chart'])
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors',
                    'wire:click' => '$dispatch("navigate-to", { route: "documents" })',
                ]),
        ];
    }
    
    private function getErrorStats(): array
    {
        return [
            Stat::make('Error', '---')
                ->description($this->errorMessage)
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger')
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors',
                    'wire:click' => 'refreshStats',
                ]),
        ];
    }
    
    public function refreshStats(): void
    {
        // Emit loading event
        $this->dispatch('stats-loading');
        
        // Clear the cache to force fresh data
        Cache::forget('dashboard_stats');
        
        // Reset error state
        $this->hasError = false;
        $this->errorMessage = '';
        
        // Trigger a re-render
        $this->dispatch('$refresh');
        
        // Emit update event after refresh
        $this->dispatch('stats-updated', [
            'significant_changes' => true,
            'timestamp' => now()->toISOString()
        ]);
    }

    private function calculateGrowth(string $type, int $current): array
    {
        // Mock growth calculation - in real implementation, this would compare with previous period
        $growthPercentage = rand(-10, 25);
        $isPositive = $growthPercentage >= 0;
        
        return [
            'description' => ($isPositive ? '+' : '') . $growthPercentage . '% from last month',
            'icon' => $isPositive ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down',
            'color' => $isPositive ? 'success' : 'danger',
            'chart' => $this->generateMockChart(),
        ];
    }

    private function generateMockChart(): array
    {
        // Generate mock chart data for the last 7 days
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $data[] = rand(10, 100);
        }
        return $data;
    }

    public function getPollingInterval(): ?string
    {
        // Refresh every 30 seconds, but only if no error occurred
        return $this->hasError ? null : '30s';
    }
    
    protected function getViewData(): array
    {
        return [
            'isLoading' => $this->isLoading,
            'hasError' => $this->hasError,
            'errorMessage' => $this->errorMessage,
        ];
    }
    
    public function mount(): void
    {
        // Initialize the widget
        $this->isLoading = false;
        $this->hasError = false;
        $this->errorMessage = '';
    }
    
    protected function getActions(): array
    {
        return [
            \Filament\Actions\Action::make('refresh')
                ->label('Refresh Data')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action('refreshStats')
                ->tooltip('Refresh statistics data'),
        ];
    }
}