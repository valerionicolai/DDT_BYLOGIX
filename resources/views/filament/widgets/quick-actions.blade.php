<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Quick Actions
        </x-slot>

        <x-slot name="description">
            Frequently used actions for faster workflow
        </x-slot>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach ($actions as $action)
                @if ($action->isVisible())
                    <div class="group">
                        <a 
                            href="{{ $action->getUrl() }}" 
                            class="flex flex-col items-center justify-center p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-primary-500 dark:hover:border-primary-400 hover:shadow-md transition-all duration-200 group-hover:scale-105"
                            title="{{ $action->getTooltip() }}"
                        >
                            <div class="flex items-center justify-center w-12 h-12 mb-3 rounded-full bg-{{ $action->getColor() }}-100 dark:bg-{{ $action->getColor() }}-900/20 text-{{ $action->getColor() }}-600 dark:text-{{ $action->getColor() }}-400 group-hover:bg-{{ $action->getColor() }}-200 dark:group-hover:bg-{{ $action->getColor() }}-900/40 transition-colors">
                                <x-filament::icon 
                                    :icon="$action->getIcon()" 
                                    class="w-6 h-6"
                                />
                            </div>
                            
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100 text-center leading-tight">
                                {{ $action->getLabel() }}
                            </span>
                        </a>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                <span>Need help? Check our documentation</span>
                <a 
                    href="#" 
                    class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium"
                >
                    View Docs →
                </a>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>