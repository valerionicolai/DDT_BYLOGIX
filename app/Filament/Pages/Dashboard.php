<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\ProjectsChart;
use App\Filament\Widgets\RecentActivity;
use App\Filament\Widgets\QuickActions;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static string $view = 'filament.pages.dashboard';
    
    protected static ?string $title = 'Dashboard';
    
    protected static ?int $navigationSort = 1;

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            ProjectsChart::class,
            RecentActivity::class,
            QuickActions::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'lg' => 3,
            'xl' => 4,
        ];
    }

    public function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
        ];
    }

    public function getFooterWidgets(): array
    {
        return [
            QuickActions::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('refresh')
                ->label('Refresh Data')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    $this->dispatch('refresh-dashboard');
                    \Filament\Notifications\Notification::make()
                        ->title('Dashboard refreshed')
                        ->success()
                        ->send();
                }),
        ];
    }
}