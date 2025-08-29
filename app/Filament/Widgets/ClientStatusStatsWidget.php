<?php

namespace App\Filament\Widgets;

use App\Services\ClientStatusService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientStatusStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $service = app(ClientStatusService::class);
        $statistics = $service->getStatusStatistics();

        return [
            Stat::make('Total Clients', $statistics['total'])
                ->description('All clients in the system')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Active Clients', $statistics['active'])
                ->description('Currently active clients')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Prospects', $statistics['prospects'])
                ->description('Potential clients')
                ->descriptionIcon('heroicon-m-eye')
                ->color('blue'),

            Stat::make('Business Ready', $statistics['business_ready'])
                ->description('Active + Prospects')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('info'),
        ];
    }
}