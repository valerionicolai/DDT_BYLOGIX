<?php

namespace App\Filament\Resources\ProjectPriorityResource\Pages;

use App\Enums\ProjectPriority;
use App\Filament\Resources\ProjectPriorityResource;
use App\Models\Project;
use App\Services\ProjectPriorityService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListProjectPriorities extends ListRecords
{
    protected static string $resource = ProjectPriorityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('auto_escalate')
                ->label('Auto Escalate Overdue')
                ->icon('heroicon-o-arrow-up')
                ->color('warning')
                ->action(function () {
                    $service = new ProjectPriorityService();
                    $escalatedCount = $service->autoEscalateProjects();
                    
                    Notification::make()
                        ->title('Auto Escalation Complete')
                        ->body("Escalated {$escalatedCount} overdue projects")
                        ->success()
                        ->send();
                }),
            Actions\Action::make('priority_statistics')
                ->label('Priority Statistics')
                ->icon('heroicon-o-chart-bar')
                ->color('info')
                ->modalContent(function () {
                    $service = new ProjectPriorityService();
                    $stats = $service->getPriorityStatistics();
                    
                    $content = '<div class="space-y-4">';
                    $content .= '<h3 class="text-lg font-semibold">Project Priority Distribution</h3>';
                    
                    foreach ($stats as $priority => $data) {
                        $priorityEnum = ProjectPriority::from($priority);
                        $content .= '<div class="flex justify-between items-center p-3 bg-gray-50 rounded">';
                        $content .= '<span class="font-medium">' . $priorityEnum->label() . '</span>';
                        $content .= '<span class="text-sm text-gray-600">' . $data['count'] . ' projects (' . number_format($data['percentage'], 1) . '%)</span>';
                        $content .= '</div>';
                    }
                    
                    $content .= '</div>';
                    
                    return view('filament::components.modal.content', [
                        'content' => $content
                    ]);
                })
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close'),
        ];
    }
}
