<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Filament\Notifications\Notification;

class StatsNotifications extends Component
{
    public $lastUpdateTime;
    public $updateCount = 0;

    public function mount()
    {
        $this->lastUpdateTime = now();
    }

    #[On('stats-updated')]
    public function handleStatsUpdate($data = [])
    {
        $this->updateCount++;
        $this->lastUpdateTime = now();

        // Show notification for significant changes
        if (isset($data['significant_changes']) && $data['significant_changes']) {
            Notification::make()
                ->title('Statistics Updated')
                ->body('Dashboard statistics have been refreshed with new data.')
                ->icon('heroicon-o-chart-bar')
                ->iconColor('success')
                ->duration(3000)
                ->send();
        }

        // Dispatch browser event for other components
        $this->dispatch('dashboard-stats-refreshed', [
            'timestamp' => $this->lastUpdateTime->toISOString(),
            'updateCount' => $this->updateCount
        ]);
    }

    #[On('stats-error')]
    public function handleStatsError($error = 'Unknown error occurred')
    {
        Notification::make()
            ->title('Statistics Error')
            ->body($error)
            ->icon('heroicon-o-exclamation-triangle')
            ->iconColor('danger')
            ->duration(5000)
            ->send();
    }

    #[On('stats-loading')]
    public function handleStatsLoading()
    {
        // Optional: Show loading notification for long operations
        if ($this->updateCount > 0) {
            Notification::make()
                ->title('Refreshing Statistics')
                ->body('Please wait while we update the dashboard data...')
                ->icon('heroicon-o-arrow-path')
                ->iconColor('info')
                ->duration(2000)
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.stats-notifications');
    }
}