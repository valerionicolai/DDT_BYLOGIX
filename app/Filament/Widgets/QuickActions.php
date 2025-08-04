<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Filament\Actions\Action;
use Filament\Support\Enums\ActionSize;
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
                ->tooltip('Create a new project'),

            Action::make('new_client')
                ->label('New Client')
                ->icon('heroicon-o-user-plus')
                ->color('success')
                ->size(ActionSize::Large)
                ->url(route('filament.admin.resources.clients.create'))
                ->tooltip('Add a new client'),

            Action::make('upload_document')
                ->label('Upload Document')
                ->icon('heroicon-o-document-plus')
                ->color('warning')
                ->size(ActionSize::Large)
                ->url(route('filament.admin.resources.documents.create'))
                ->tooltip('Upload a new document'),

            Action::make('new_material')
                ->label('New Material Type')
                ->icon('heroicon-o-cube')
                ->color('info')
                ->size(ActionSize::Large)
                ->url(route('filament.admin.resources.material-types.create'))
                ->tooltip('Create a new material type'),

            Action::make('view_reports')
                ->label('View Reports')
                ->icon('heroicon-o-chart-bar')
                ->color('gray')
                ->size(ActionSize::Large)
                ->url(route('filament.admin.pages.reports'))
                ->tooltip('View system reports'),

            Action::make('system_settings')
                ->label('Settings')
                ->icon('heroicon-o-cog-6-tooth')
                ->color('gray')
                ->size(ActionSize::Large)
                ->url(route('filament.admin.pages.settings'))
                ->tooltip('System settings')
                ->visible(fn () => Auth::user()?->hasRole('super_admin') ?? false),
        ];
    }

    public function getViewData(): array
    {
        return [
            'actions' => $this->getActions(),
        ];
    }
}