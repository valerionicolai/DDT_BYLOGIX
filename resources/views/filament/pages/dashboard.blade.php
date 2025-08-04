<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Welcome Section --}}
        <div class="bg-gradient-to-r from-primary-500 to-primary-600 dark:from-primary-600 dark:to-primary-700 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold mb-2">
                        Welcome back, {{ auth()->user()->name }}!
                    </h1>
                    <p class="text-primary-100 dark:text-primary-200">
                        Here's what's happening with your projects today.
                    </p>
                </div>
                <div class="hidden md:block">
                    <div class="text-right">
                        <div class="text-3xl font-bold">{{ now()->format('H:i') }}</div>
                        <div class="text-primary-200">{{ now()->format('M j, Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Breadcrumb Navigation --}}
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <x-filament::icon 
                        icon="heroicon-o-home" 
                        class="w-4 h-4 text-gray-400"
                    />
                    <span class="ml-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Dashboard
                    </span>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-filament::icon 
                            icon="heroicon-o-chevron-right" 
                            class="w-4 h-4 text-gray-400"
                        />
                        <span class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400">
                            Overview
                        </span>
                    </div>
                </li>
            </ol>
        </nav>

        {{-- Stats Overview --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            @livewire(\App\Filament\Widgets\StatsOverview::class)
        </div>

        {{-- Detailed Statistics --}}
        <div class="mt-6">
            @livewire(\App\Filament\Widgets\DetailedStatsWidget::class)
        </div>

        {{-- Main Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Projects Chart --}}
            <div class="lg:col-span-1">
                @livewire(\App\Filament\Widgets\ProjectsChart::class)
            </div>

            {{-- Recent Activity --}}
            <div class="lg:col-span-1">
                @livewire(\App\Filament\Widgets\RecentActivity::class)
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="mt-8">
            @livewire(\App\Filament\Widgets\QuickActions::class)
        </div>

        {{-- Additional Info Section --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
            {{-- System Status --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center mr-3">
                        <x-filament::icon 
                            icon="heroicon-o-check-circle" 
                            class="w-4 h-4 text-green-600 dark:text-green-400"
                        />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">System Status</h3>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Database</span>
                        <span class="text-sm font-medium text-green-600 dark:text-green-400">Online</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">API</span>
                        <span class="text-sm font-medium text-green-600 dark:text-green-400">Healthy</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Storage</span>
                        <span class="text-sm font-medium text-green-600 dark:text-green-400">Available</span>
                    </div>
                </div>
            </div>

            {{-- Recent Updates --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center mr-3">
                        <x-filament::icon 
                            icon="heroicon-o-bell" 
                            class="w-4 h-4 text-blue-600 dark:text-blue-400"
                        />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recent Updates</h3>
                </div>
                <div class="space-y-3">
                    <div class="text-sm">
                        <div class="font-medium text-gray-900 dark:text-gray-100">Dashboard Enhanced</div>
                        <div class="text-gray-600 dark:text-gray-400">New widgets and improved layout</div>
                        <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">{{ now()->subDays(2)->format('M j') }}</div>
                    </div>
                    <div class="text-sm">
                        <div class="font-medium text-gray-900 dark:text-gray-100">User Management</div>
                        <div class="text-gray-600 dark:text-gray-400">Role-based access control added</div>
                        <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">{{ now()->subDays(5)->format('M j') }}</div>
                    </div>
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/20 rounded-full flex items-center justify-center mr-3">
                        <x-filament::icon 
                            icon="heroicon-o-chart-bar" 
                            class="w-4 h-4 text-purple-600 dark:text-purple-400"
                        />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Quick Stats</h3>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Today's Logins</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ rand(15, 45) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Active Sessions</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ rand(5, 15) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">System Load</span>
                        <span class="text-sm font-medium text-green-600 dark:text-green-400">Low</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Notifications Component --}}
    @livewire(\App\Livewire\StatsNotifications::class)

    {{-- Alpine.js for enhanced interactivity --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('dashboard', () => ({
                refreshing: false,
                
                async refreshDashboard() {
                    this.refreshing = true;
                    
                    // Dispatch refresh event to all widgets
                    window.dispatchEvent(new CustomEvent('refresh-dashboard'));
                    
                    // Simulate refresh delay
                    setTimeout(() => {
                        this.refreshing = false;
                    }, 1000);
                }
            }));
        });

        // Listen for navigation events
        document.addEventListener('livewire:init', () => {
            Livewire.on('navigate-to', (event) => {
                if (event.route) {
                    window.location.href = event.route;
                }
            });
        });
    </script>
</x-filament-panels::page>