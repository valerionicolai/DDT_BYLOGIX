<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Filament\Actions\Action;
use Filament\Support\Enums\ActionSize;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class QuickActions extends Widget
{
    protected static string $view = 'filament.widgets.quick-actions';
    
    protected static ?int $sort = 4;
    
    protected int | string | array $columnSpan = 'full';

    public function getActions(): array
    {
        return [
            Action::make('new_project')
                ->label('New Project')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->size(ActionSize::Large)
                ->url(route('filament.admin.resources.projects.create'))
                ->tooltip('Create a new project (Ctrl+Shift+P)')
                ->keyBindings(['ctrl+shift+p', 'cmd+shift+p']),

            Action::make('new_client')
                ->label('New Client')
                ->icon('heroicon-o-user-plus')
                ->color('success')
                ->size(ActionSize::Large)
                ->url(route('filament.admin.resources.clients.create'))
                ->tooltip('Add a new client (Ctrl+Shift+C)')
                ->keyBindings(['ctrl+shift+c', 'cmd+shift+c']),

            Action::make('upload_document')
                ->label('Upload Document')
                ->icon('heroicon-o-document-plus')
                ->color('warning')
                ->size(ActionSize::Large)
                ->url(route('filament.admin.resources.documents.create'))
                ->tooltip('Upload a new document (Ctrl+Shift+D)')
                ->keyBindings(['ctrl+shift+d', 'cmd+shift+d']),

            Action::make('new_material')
                ->label('New Material Type')
                ->icon('heroicon-o-cube')
                ->color('info')
                ->size(ActionSize::Large)
                ->url(route('filament.admin.resources.material-types.create'))
                ->tooltip('Create a new material type (Ctrl+Shift+M)')
                ->keyBindings(['ctrl+shift+m', 'cmd+shift+m']),

            Action::make('manage_document_types')
                ->label('Document Types')
                ->icon('heroicon-o-document-text')
                ->color('indigo')
                ->size(ActionSize::Large)
                ->url(route('filament.admin.resources.document-types.index'))
                ->tooltip('Manage document types (Ctrl+Shift+T)')
                ->keyBindings(['ctrl+shift+t', 'cmd+shift+t']),

            Action::make('manage_document_categories')
                ->label('Document Categories')
                ->icon('heroicon-o-folder-open')
                ->color('emerald')
                ->size(ActionSize::Large)
                ->url(route('filament.admin.resources.document-categories.index'))
                ->tooltip('Manage document categories (Ctrl+Shift+G)')
                ->keyBindings(['ctrl+shift+g', 'cmd+shift+g']),

            
            Action::make('quick_search')
                ->label('Quick Search')
                ->icon('heroicon-o-magnifying-glass')
                ->color('purple')
                ->size(ActionSize::Large)
                ->tooltip('Open quick search (Ctrl+K)')
                ->keyBindings(['ctrl+k', 'cmd+k'])
                ->action(function () {
                    $this->dispatch('open-quick-search');
                    
                    Notification::make()
                        ->title('Quick Search')
                        ->body('Search functionality opened')
                        ->icon('heroicon-o-magnifying-glass')
                        ->iconColor('success')
                        ->duration(2000)
                        ->send();
                }),

            Action::make('help_shortcuts')
                ->label('Keyboard Shortcuts')
                ->icon('heroicon-o-question-mark-circle')
                ->color('gray')
                ->size(ActionSize::Large)
                ->tooltip('View keyboard shortcuts (Ctrl+?)')
                ->keyBindings(['ctrl+shift+slash', 'cmd+shift+slash'])
                ->action(function () {
                    $this->dispatch('show-shortcuts-modal');
                    
                    Notification::make()
                        ->title('Keyboard Shortcuts')
                        ->body('Shortcuts reference opened')
                        ->icon('heroicon-o-question-mark-circle')
                        ->iconColor('info')
                        ->duration(2000)
                        ->send();
                }),
        ];
    }

    public function getViewData(): array
    {
        return [
            'actions' => $this->getActions(),
            'shortcuts' => $this->getKeyboardShortcuts(),
        ];
    }

    public function getKeyboardShortcuts(): array
    {
        return [
            'navigation' => [
                'Ctrl+Shift+P' => 'New Project',
                'Ctrl+Shift+C' => 'New Client',
                'Ctrl+Shift+D' => 'Upload Document',
                'Ctrl+Shift+M' => 'New Material Type',
                'Ctrl+Shift+T' => 'Document Types',
                'Ctrl+Shift+G' => 'Document Categories',
                'Ctrl+K' => 'Quick Search',
                'Ctrl+?' => 'Show Shortcuts',
            ],
            'general' => [
                'Escape' => 'Close Modal/Cancel',
                'Enter' => 'Confirm Action',
                'Tab' => 'Navigate Fields',
                'Shift+Tab' => 'Navigate Fields (Reverse)',
            ]
        ];
    }

    public function callAction(string $actionName)
    {
        $actions = collect($this->getActions())->keyBy(fn($action) => $action->getName());
        
        if ($actions->has($actionName)) {
            $action = $actions->get($actionName);
            
            // Execute the action if it has a closure
            if ($action->getAction()) {
                return $action->getAction()();
            }
            
            // If it has a URL, redirect to it
            if ($action->getUrl()) {
                return redirect($action->getUrl());
            }
        }
        
        // Fallback notification
        Notification::make()
            ->title('Action Not Found')
            ->body("The action '{$actionName}' could not be executed.")
            ->icon('heroicon-o-exclamation-triangle')
            ->iconColor('warning')
            ->duration(3000)
            ->send();
    }
}