<?php

namespace App\Filament\Widgets;

use App\Services\ClientStatusService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientStatusStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $service = app(ClientStatusService::class);
        $statistics = $service->getStatusStatistics();

        return [
            Stat::make('Totale Clienti', $statistics['total'])
                ->description('Tutti i clienti nel sistema')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Clienti Attivi', $statistics['active'])
                ->description('Clienti attualmente attivi')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Prospetti', $statistics['prospects'])
                ->description('Potenziali clienti')
                ->descriptionIcon('heroicon-m-eye')
                ->color('blue'),

            Stat::make('Pronti per Business', $statistics['business_ready'])
                ->description('Attivi + Prospetti')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('info'),
        ];
    }
}