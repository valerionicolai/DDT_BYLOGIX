<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Recent Activity
        </x-slot>

        <x-slot name="description">
            Latest updates and changes in the system
        </x-slot>

        <div class="space-y-4">
            @forelse ($activities as $activity)
                <div class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    {{-- Activity Icon --}}
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-{{ $activity['color'] }}-100 dark:bg-{{ $activity['color'] }}-900/20 rounded-full flex items-center justify-center">
                            <x-filament::icon 
                                :icon="$activity['icon']" 
                                class="w-4 h-4 text-{{ $activity['color'] }}-600 dark:text-{{ $activity['color'] }}-400"
                            />
                        </div>
                    </div>

                    {{-- Activity Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                    {{ $activity['title'] }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    {{ $activity['description'] }}
                                </p>
                                <div class="flex items-center mt-2 space-x-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                        {{ $activity['user_name'] }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $activity['created_at']->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                            
                            {{-- Action Button --}}
                            <div class="flex-shrink-0 ml-2">
                                <a 
                                    href="{{ $activity['url'] }}" 
                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                                    title="View details"
                                >
                                    <x-filament::icon 
                                        icon="heroicon-o-eye" 
                                        class="w-4 h-4"
                                    />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <x-filament::icon 
                        icon="heroicon-o-clock" 
                        class="w-12 h-12 text-gray-400 dark:text-gray-500 mx-auto mb-4"
                    />
                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">
                        No recent activity
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Activity will appear here as users interact with the system.
                    </p>
                </div>
            @endforelse
        </div>

        @if (count($activities) > 0)
            <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="text-center">
                    <a 
                        href="#" 
                        class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300"
                    >
                        View all activity →
                    </a>
                </div>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>