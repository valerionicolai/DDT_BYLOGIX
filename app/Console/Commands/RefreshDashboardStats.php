<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\StatsRefreshService;

class RefreshDashboardStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:refresh-stats 
                            {--force : Force refresh all cache}
                            {--key= : Refresh specific cache key}
                            {--show-expiry : Show cache expiry times}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh dashboard statistics cache';

    protected StatsRefreshService $statsService;

    public function __construct(StatsRefreshService $statsService)
    {
        parent::__construct();
        $this->statsService = $statsService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Refreshing Dashboard Statistics...');
        $this->newLine();

        // Show cache expiry times if requested
        if ($this->option('show-expiry')) {
            $this->showCacheExpiry();
            return;
        }

        // Refresh specific cache key if provided
        if ($key = $this->option('key')) {
            $this->refreshSpecificKey($key);
            return;
        }

        // Force refresh all cache
        if ($this->option('force')) {
            $this->forceRefreshAll();
            return;
        }

        // Default: refresh all stats
        $this->refreshAll();
    }

    protected function refreshAll()
    {
        $this->info('Refreshing all dashboard statistics...');
        
        $result = $this->statsService->refreshAllStats();
        
        if ($result['success']) {
            $this->info('✅ All statistics refreshed successfully!');
            $this->newLine();
            
            $this->table(
                ['Component', 'Status'],
                [
                    ['Basic Stats', '✅ Refreshed'],
                    ['Detailed Stats', '✅ Refreshed'],
                    ['Chart Data', '✅ Refreshed'],
                    ['Activity Data', '✅ Refreshed'],
                ]
            );
            
            $this->info("🕒 Refreshed at: {$result['timestamp']}");
        } else {
            $this->error('❌ Failed to refresh statistics');
            $this->error("Error: {$result['message']}");
        }
    }

    protected function forceRefreshAll()
    {
        $this->warn('🔥 Force refreshing all cache...');
        
        $cacheKeys = [
            'dashboard_stats' => 'Basic Stats',
            'detailed_dashboard_stats' => 'Detailed Stats',
            'projects_chart_data' => 'Chart Data',
            'recent_activity_data' => 'Activity Data'
        ];

        $results = [];
        
        foreach ($cacheKeys as $key => $name) {
            $success = $this->statsService->forceRefresh($key);
            $results[] = [$name, $success ? '✅ Refreshed' : '❌ Failed'];
        }

        $this->table(['Component', 'Status'], $results);
    }

    protected function refreshSpecificKey(string $key)
    {
        $this->info("Refreshing cache key: {$key}");
        
        $success = $this->statsService->forceRefresh($key);
        
        if ($success) {
            $this->info("✅ Cache key '{$key}' refreshed successfully!");
        } else {
            $this->error("❌ Failed to refresh cache key '{$key}'");
        }
    }

    protected function showCacheExpiry()
    {
        $this->info('📊 Cache Expiry Information');
        $this->newLine();

        $cacheKeys = [
            'dashboard_stats' => 'Basic Stats',
            'detailed_dashboard_stats' => 'Detailed Stats',
            'projects_chart_data' => 'Chart Data',
            'recent_activity_data' => 'Activity Data'
        ];

        $results = [];
        
        foreach ($cacheKeys as $key => $name) {
            $needsRefresh = $this->statsService->needsRefresh($key);
            $expiry = $this->statsService->getCacheExpiry($key);
            
            $status = $needsRefresh ? '❌ Expired' : '✅ Valid';
            $expiryText = $expiry ? "{$expiry}s remaining" : 'Unknown';
            
            $results[] = [$name, $key, $status, $expiryText];
        }

        $this->table(
            ['Component', 'Cache Key', 'Status', 'Expiry'],
            $results
        );
    }
}
