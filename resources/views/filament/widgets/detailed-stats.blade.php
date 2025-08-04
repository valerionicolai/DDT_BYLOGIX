<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <x-filament::icon 
                        icon="heroicon-o-chart-bar-square" 
                        class="w-5 h-5 text-gray-500 dark:text-gray-400"
                    />
                    <span>Detailed Statistics</span>
                </div>
                <div class="flex items-center space-x-2">
                    @if($hasError)
                        <x-filament::badge color="danger" icon="heroicon-o-exclamation-triangle">
                            Error
                        </x-filament::badge>
                    @endif
                    @if($isLoading)
                        <x-filament::loading-indicator class="w-4 h-4" />
                    @endif
                    <x-filament::button
                        wire:click="refreshStats"
                        size="sm"
                        color="gray"
                        icon="heroicon-o-arrow-path"
                        tooltip="Refresh Statistics"
                    >
                        Refresh
                    </x-filament::button>
                </div>
            </div>
        </x-slot>

        @if($hasError)
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <div class="flex items-center">
                    <x-filament::icon 
                        icon="heroicon-o-exclamation-triangle" 
                        class="w-5 h-5 text-red-600 dark:text-red-400 mr-2"
                    />
                    <span class="text-red-800 dark:text-red-200">{{ $errorMessage }}</span>
                </div>
            </div>
        @elseif($isLoading)
            <x-stats-loading count="4" />
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Project Statistics --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center mr-3">
                            <x-filament::icon 
                                icon="heroicon-o-briefcase" 
                                class="w-5 h-5 text-blue-600 dark:text-blue-400"
                            />
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Projects</h3>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $stats['projects']['total'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Active</span>
                            <span class="text-sm font-medium text-green-600 dark:text-green-400">{{ $stats['projects']['active'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Completed</span>
                            <span class="text-sm font-medium text-blue-600 dark:text-blue-400">{{ $stats['projects']['completed'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Overdue</span>
                            <span class="text-sm font-medium text-red-600 dark:text-red-400">{{ $stats['projects']['overdue'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">This Month</span>
                            <span class="text-sm font-medium text-purple-600 dark:text-purple-400">{{ $stats['projects']['this_month'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                {{-- Client Statistics --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center mr-3">
                            <x-filament::icon 
                                icon="heroicon-o-users" 
                                class="w-5 h-5 text-green-600 dark:text-green-400"
                            />
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Clients</h3>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $stats['clients']['total'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Active</span>
                            <span class="text-sm font-medium text-green-600 dark:text-green-400">{{ $stats['clients']['active'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Inactive</span>
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $stats['clients']['inactive'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">This Month</span>
                            <span class="text-sm font-medium text-purple-600 dark:text-purple-400">{{ $stats['clients']['this_month'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Engagement</span>
                            <span class="text-sm font-medium text-blue-600 dark:text-blue-400">{{ $stats['performance']['client_engagement'] ?? 0 }}%</span>
                        </div>
                    </div>
                </div>

                {{-- Material Statistics --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/20 rounded-full flex items-center justify-center mr-3">
                            <x-filament::icon 
                                icon="heroicon-o-cube" 
                                class="w-5 h-5 text-orange-600 dark:text-orange-400"
                            />
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Materials</h3>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total Types</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $stats['materials']['total'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Active</span>
                            <span class="text-sm font-medium text-green-600 dark:text-green-400">{{ $stats['materials']['active'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Inactive</span>
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $stats['materials']['inactive'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Categories</span>
                            <span class="text-sm font-medium text-purple-600 dark:text-purple-400">{{ $stats['materials']['categories'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                {{-- Document Statistics --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/20 rounded-full flex items-center justify-center mr-3">
                            <x-filament::icon 
                                icon="heroicon-o-document-text" 
                                class="w-5 h-5 text-purple-600 dark:text-purple-400"
                            />
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Documents</h3>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $stats['documents']['total'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Active</span>
                            <span class="text-sm font-medium text-green-600 dark:text-green-400">{{ $stats['documents']['active'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Draft</span>
                            <span class="text-sm font-medium text-yellow-600 dark:text-yellow-400">{{ $stats['documents']['draft'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Archived</span>
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $stats['documents']['archived'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">This Month</span>
                            <span class="text-sm font-medium text-purple-600 dark:text-purple-400">{{ $stats['documents']['this_month'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Performance Metrics --}}
            <div class="mt-6 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-lg border border-blue-200 dark:border-blue-800 p-6">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center mr-3">
                        <x-filament::icon 
                            icon="heroicon-o-chart-pie" 
                            class="w-5 h-5 text-white"
                        />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Performance Metrics</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['performance']['completion_rate'] ?? 0 }}%</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Project Completion Rate</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $stats['performance']['active_rate'] ?? 0 }}%</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Active Project Rate</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['performance']['client_engagement'] ?? 0 }}%</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Client Engagement</div>
                    </div>
                </div>
            </div>

            {{-- Last Updated --}}
            @if(isset($stats['last_updated']))
                <div class="mt-4 text-center">
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        Last updated: {{ $stats['last_updated'] }}
                    </span>
                </div>
            @endif
        @endif
    </x-filament::section>
</x-filament-widgets::widget>