<div 
    x-data="quickActionsWidget()" 
    x-init="initKeyboardShortcuts()"
    @open-quick-search.window="openQuickSearch()"
    @show-shortcuts-modal.window="showShortcutsModal = true"
>
    <x-filament-widgets::widget>
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center justify-between">
                    <span>Quick Actions</span>
                    <button 
                        @click="showShortcutsModal = true"
                        class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
                        title="View keyboard shortcuts (Ctrl+?)"
                    >
                        <x-filament::icon icon="heroicon-o-question-mark-circle" class="w-4 h-4" />
                    </button>
                </div>
            </x-slot>

            <x-slot name="description">
                Frequently used actions for faster workflow. Use keyboard shortcuts for quick access.
            </x-slot>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($actions as $action)
                    @if ($action->isVisible())
                        <div class="group">
                            @if($action->getUrl())
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
                            @else
                                <button 
                                    wire:click="callAction('{{ $action->getName() }}')"
                                    class="w-full flex flex-col items-center justify-center p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-primary-500 dark:hover:border-primary-400 hover:shadow-md transition-all duration-200 group-hover:scale-105"
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
                                </button>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                    <span>Press <kbd class="px-1 py-0.5 text-xs bg-gray-100 dark:bg-gray-700 rounded">Ctrl+?</kbd> for shortcuts</span>
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

    <!-- Quick Search Modal -->
    <div 
        x-show="showQuickSearch" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showQuickSearch = false"></div>

            <div 
                x-show="showQuickSearch"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl"
            >
                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100 mb-4">Quick Search</h3>
                <input 
                    x-ref="searchInput"
                    type="text" 
                    placeholder="Search projects, clients, documents..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-gray-100"
                    @keydown.escape="showQuickSearch = false"
                >
                <div class="mt-4 flex justify-end space-x-2">
                    <button 
                        @click="showQuickSearch = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600"
                    >
                        Cancel
                    </button>
                    <button 
                        class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-md hover:bg-primary-700"
                    >
                        Search
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Keyboard Shortcuts Modal -->
    <div 
        x-show="showShortcutsModal" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showShortcutsModal = false"></div>

            <div 
                x-show="showShortcutsModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl"
            >
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">Keyboard Shortcuts</h3>
                    <button 
                        @click="showShortcutsModal = false"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                    >
                        <x-filament::icon icon="heroicon-o-x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">Navigation</h4>
                        <div class="space-y-2">
                            @foreach ($shortcuts['navigation'] as $key => $description)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $description }}</span>
                                    <kbd class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded">{{ $key }}</kbd>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">General</h4>
                        <div class="space-y-2">
                            @foreach ($shortcuts['general'] as $key => $description)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $description }}</span>
                                    <kbd class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded">{{ $key }}</kbd>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button 
                        @click="showShortcutsModal = false"
                        class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-md hover:bg-primary-700"
                    >
                        Got it
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function quickActionsWidget() {
    return {
        showQuickSearch: false,
        showShortcutsModal: false,

        initKeyboardShortcuts() {
            document.addEventListener('keydown', (e) => {
                // Check for modifier keys
                const isCtrl = e.ctrlKey || e.metaKey;
                const isShift = e.shiftKey;

                // Quick search (Ctrl+K)
                if (isCtrl && e.key === 'k') {
                    e.preventDefault();
                    this.openQuickSearch();
                }

                // Show shortcuts (Ctrl+?)
                if (isCtrl && isShift && e.key === '?') {
                    e.preventDefault();
                    this.showShortcutsModal = true;
                }

                // Navigation shortcuts
                if (isCtrl && isShift) {
                    switch (e.key.toLowerCase()) {
                        case 'p':
                            e.preventDefault();
                            window.location.href = '{{ route("filament.admin.resources.projects.create") }}';
                            break;
                        case 'c':
                            e.preventDefault();
                            window.location.href = '{{ route("filament.admin.resources.clients.create") }}';
                            break;
                        case 'd':
                            e.preventDefault();
                            window.location.href = '{{ route("filament.admin.resources.documents.create") }}';
                            break;
                        case 'm':
                            e.preventDefault();
                            window.location.href = '{{ route("filament.admin.resources.material-types.create") }}';
                            break;
                        case 't':
                            e.preventDefault();
                            window.location.href = '{{ route("filament.admin.resources.document-types.index") }}';
                            break;
                        case 'g':
                            e.preventDefault();
                            window.location.href = '{{ route("filament.admin.resources.document-categories.index") }}';
                            break;

                    }
                }

                // Close modals with Escape
                if (e.key === 'Escape') {
                    this.showQuickSearch = false;
                    this.showShortcutsModal = false;
                }
            });
        },

        openQuickSearch() {
            this.showQuickSearch = true;
            this.$nextTick(() => {
                this.$refs.searchInput?.focus();
            });
        }
    }
}
</script>