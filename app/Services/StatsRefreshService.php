<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Project;
use App\Models\Client;
use App\Models\MaterialType;
use App\Models\Document;

class StatsRefreshService
{
    protected array $cacheKeys = [
        'dashboard_stats',
        'detailed_dashboard_stats',
        'projects_chart_data',
        'recent_activity_data'
    ];

    protected int $defaultCacheDuration = 300; // 5 minutes

    /**
     * Refresh all dashboard statistics
     */
    public function refreshAllStats(): array
    {
        try {
            $results = [];
            
            // Clear all cache keys
            foreach ($this->cacheKeys as $key) {
                Cache::forget($key);
                $results[$key] = 'cleared';
            }

            // Pre-warm cache with fresh data
            $results['basic_stats'] = $this->refreshBasicStats();
            $results['detailed_stats'] = $this->refreshDetailedStats();
            $results['chart_data'] = $this->refreshChartData();
            $results['activity_data'] = $this->refreshActivityData();

            Log::info('Dashboard statistics refreshed successfully', $results);

            return [
                'success' => true,
                'message' => 'All statistics refreshed successfully',
                'data' => $results,
                'timestamp' => now()->toISOString()
            ];

        } catch (\Exception $e) {
            Log::error('Failed to refresh dashboard statistics: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to refresh statistics',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ];
        }
    }

    /**
     * Refresh basic statistics
     */
    public function refreshBasicStats(): array
    {
        return Cache::remember('dashboard_stats', $this->defaultCacheDuration, function () {
            return [
                'total_projects' => Project::count(),
                'active_projects' => Project::where('status', 'active')->count(),
                'completed_projects' => Project::where('status', 'completed')->count(),
                'total_clients' => Client::count(),
                'active_clients' => Client::where('status', 'active')->count(),
                'total_materials' => MaterialType::count(),
                'active_materials' => MaterialType::where('is_active', true)->count(),
                'total_documents' => Document::count(),
                'last_updated' => now()->toISOString()
            ];
        });
    }

    /**
     * Refresh detailed statistics
     */
    public function refreshDetailedStats(): array
    {
        return Cache::remember('detailed_dashboard_stats', $this->defaultCacheDuration, function () {
            $totalProjects = Project::count();
            $activeProjects = Project::where('status', 'active')->count();
            $completedProjects = Project::where('status', 'completed')->count();
            
            $totalClients = Client::count();
            $activeClients = Client::where('status', 'active')->count();
            
            return [
                'projects' => [
                    'total' => $totalProjects,
                    'active' => $activeProjects,
                    'completed' => $completedProjects,
                    'overdue' => Project::where('due_date', '<', now())->where('status', '!=', 'completed')->count(),
                    'this_month' => Project::whereMonth('created_at', now()->month)->count(),
                ],
                'clients' => [
                    'total' => $totalClients,
                    'active' => $activeClients,
                    'inactive' => $totalClients - $activeClients,
                    'this_month' => Client::whereMonth('created_at', now()->month)->count(),
                ],
                'materials' => [
                    'total' => MaterialType::count(),
                    'active' => MaterialType::where('is_active', true)->count(),
                    'inactive' => MaterialType::where('is_active', false)->count(),
                    'categories' => MaterialType::distinct('category')->count('category'),
                ],
                'documents' => [
                    'total' => Document::count(),
                    'active' => Document::where('status', 'active')->count(),
                    'draft' => Document::where('status', 'draft')->count(),
                    'archived' => Document::where('status', 'archived')->count(),
                    'this_month' => Document::whereMonth('created_at', now()->month)->count(),
                ],
                'performance' => [
                    'completion_rate' => $totalProjects > 0 ? round(($completedProjects / $totalProjects) * 100, 1) : 0,
                    'active_rate' => $totalProjects > 0 ? round(($activeProjects / $totalProjects) * 100, 1) : 0,
                    'client_engagement' => $totalClients > 0 ? round(($activeClients / $totalClients) * 100, 1) : 0,
                ],
                'last_updated' => now()->format('M j, Y \a\t g:i A')
            ];
        });
    }

    /**
     * Refresh chart data
     */
    public function refreshChartData(): array
    {
        return Cache::remember('projects_chart_data', $this->defaultCacheDuration, function () {
            return [
                'labels' => ['Active', 'Completed', 'On Hold', 'Cancelled'],
                'data' => [
                    Project::where('status', 'active')->count(),
                    Project::where('status', 'completed')->count(),
                    Project::where('status', 'on_hold')->count(),
                    Project::where('status', 'cancelled')->count(),
                ],
                'last_updated' => now()->toISOString()
            ];
        });
    }

    /**
     * Refresh activity data
     */
    public function refreshActivityData(): array
    {
        return Cache::remember('recent_activity_data', $this->defaultCacheDuration, function () {
            $activities = [];
            
            // Get recent projects
            $recentProjects = Project::latest()->take(3)->get();
            foreach ($recentProjects as $project) {
                $activities[] = [
                    'type' => 'project',
                    'title' => "Project '{$project->name}' was created",
                    'time' => $project->created_at->diffForHumans(),
                    'icon' => 'heroicon-o-briefcase',
                    'color' => 'blue'
                ];
            }
            
            // Get recent clients
            $recentClients = Client::latest()->take(2)->get();
            foreach ($recentClients as $client) {
                $activities[] = [
                    'type' => 'client',
                    'title' => "Client '{$client->name}' was added",
                    'time' => $client->created_at->diffForHumans(),
                    'icon' => 'heroicon-o-users',
                    'color' => 'green'
                ];
            }
            
            // Sort by creation time
            usort($activities, function ($a, $b) {
                return strcmp($b['time'], $a['time']);
            });
            
            return array_slice($activities, 0, 5);
        });
    }

    /**
     * Check if cache needs refresh
     */
    public function needsRefresh(string $cacheKey): bool
    {
        return !Cache::has($cacheKey);
    }

    /**
     * Get cache expiry time
     */
    public function getCacheExpiry(string $cacheKey): ?int
    {
        try {
            // For file cache, we can't get TTL, so return null
            if (config('cache.default') === 'file') {
                return null;
            }
            
            // For other cache drivers, check if key exists
            return Cache::has($cacheKey) ? $this->defaultCacheDuration : null;
        } catch (\Exception $e) {
            Log::warning("Could not get cache expiry for {$cacheKey}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Force refresh specific cache key
     */
    public function forceRefresh(string $cacheKey): bool
    {
        try {
            Cache::forget($cacheKey);
            
            switch ($cacheKey) {
                case 'dashboard_stats':
                    $this->refreshBasicStats();
                    break;
                case 'detailed_dashboard_stats':
                    $this->refreshDetailedStats();
                    break;
                case 'projects_chart_data':
                    $this->refreshChartData();
                    break;
                case 'recent_activity_data':
                    $this->refreshActivityData();
                    break;
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to refresh cache key {$cacheKey}: " . $e->getMessage());
            return false;
        }
    }
}