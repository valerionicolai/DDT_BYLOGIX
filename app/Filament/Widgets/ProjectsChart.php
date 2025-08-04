<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Project;
use Illuminate\Support\Facades\Cache;

class ProjectsChart extends ChartWidget
{
    protected static ?string $heading = 'Project Status Distribution';
    
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = [
        'sm' => 2,
        'md' => 2,
        'lg' => 2,
        'xl' => 2,
    ];

    protected static ?string $maxHeight = '300px';

    public ?string $filter = 'all';

    protected function getData(): array
    {
        return Cache::remember("projects_chart_{$this->filter}", 300, function () {
            $query = Project::query();
            
            // Apply filter if needed
            if ($this->filter !== 'all') {
                $query->where('status', $this->filter);
            }

            // Get project counts by status
            $statusCounts = $query->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            // Define status colors and labels
            $statusConfig = [
                'planning' => ['label' => 'Planning', 'color' => '#f59e0b'],
                'active' => ['label' => 'Active', 'color' => '#10b981'],
                'on_hold' => ['label' => 'On Hold', 'color' => '#f97316'],
                'completed' => ['label' => 'Completed', 'color' => '#3b82f6'],
                'cancelled' => ['label' => 'Cancelled', 'color' => '#ef4444'],
            ];

            $labels = [];
            $data = [];
            $colors = [];

            foreach ($statusConfig as $status => $config) {
                if (isset($statusCounts[$status]) && $statusCounts[$status] > 0) {
                    $labels[] = $config['label'];
                    $data[] = $statusCounts[$status];
                    $colors[] = $config['color'];
                }
            }

            return [
                'datasets' => [
                    [
                        'label' => 'Projects',
                        'data' => $data,
                        'backgroundColor' => $colors,
                        'borderColor' => $colors,
                        'borderWidth' => 2,
                    ],
                ],
                'labels' => $labels,
            ];
        });
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                    ],
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed * 100) / total).toFixed(1);
                            return context.label + ": " + context.parsed + " (" + percentage + "%)";
                        }',
                    ],
                ],
            ],
            'cutout' => '60%',
            'animation' => [
                'animateRotate' => true,
                'animateScale' => true,
            ],
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            'all' => 'All Projects',
            'active' => 'Active Only',
            'completed' => 'Completed Only',
            'planning' => 'Planning Only',
        ];
    }

    public function getPollingInterval(): ?string
    {
        return '60s'; // Refresh every minute
    }
}