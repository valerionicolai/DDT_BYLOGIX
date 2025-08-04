<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Project;
use App\Models\Client;
use App\Models\MaterialType;
use App\Models\Document;
use Illuminate\Support\Facades\Cache;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // Cache the stats for 5 minutes to improve performance
        return Cache::remember('dashboard_stats', 300, function () {
            $totalProjects = Project::count();
            $activeProjects = Project::where('status', 'active')->count();
            $totalClients = Client::count();
            $totalMaterials = MaterialType::count();
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

                Stat::make('Total Clients', $totalClients)
                    ->description($clientGrowth['description'])
                    ->descriptionIcon($clientGrowth['icon'])
                    ->color($clientGrowth['color'])
                    ->chart($clientGrowth['chart'])
                    ->extraAttributes([
                        'class' => 'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors',
                        'wire:click' => '$dispatch("navigate-to", { route: "clients" })',
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
        });
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
        return '30s'; // Refresh every 30 seconds
    }
}