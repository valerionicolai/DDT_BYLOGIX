<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Enums\ProjectState;
use App\Enums\ProjectPriority;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Support\Enums\FontWeight;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Number;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('add_document')
                ->label('Add Document')
                ->icon('heroicon-o-document-plus')
                ->color('success')
                ->tooltip('Add a new document to this project')
                ->url(fn () => \App\Filament\Resources\DocumentResource::getUrl('create', [
                    'project_id' => $this->record->id,
                    'client_id' => $this->record->client_id
                ])),

            Actions\Action::make('view_documents')
                ->label('Documents')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->tooltip('View project documents')
                ->action(function () {
                    $this->js(<<<'JS'
                        const tabs = Array.from(document.querySelectorAll('[role="tab"]'));
                        const target = tabs.find(el => el.textContent && el.textContent.trim().toLowerCase().includes('documents'));
                        target?.click();
                    JS);
                }),

            Actions\Action::make('view_materials')
                ->label('Materials')
                ->icon('heroicon-o-cube')
                ->color('warning')
                ->tooltip('View project materials')
                ->action(function () {
                    $this->js(<<<'JS'
                        const tabs = Array.from(document.querySelectorAll('[role="tab"]'));
                        const target = tabs.find(el => el.textContent && el.textContent.trim().toLowerCase().includes('materials'));
                        target?.click();
                    JS);
                }),

            Actions\EditAction::make()
                ->label('Edit Project')
                ->icon('heroicon-o-pencil-square')
                ->color('primary')
                ->tooltip('Edit this project'),
        ];
    }

    protected function formatMoney($amount): string
    {
        if ($amount === null || $amount === '') {
            return '-';
        }

        if (class_exists(\NumberFormatter::class)) {
            try {
                return \Illuminate\Support\Number::currency($amount, 'EUR', locale: 'it');
            } catch (\Throwable $e) {
                // Fall through to basic formatter
            }
        }

        $symbol = '€';
        $formatted = number_format((float) $amount, 2, ',', '.');
        return trim($symbol . ' ' . $formatted);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Enhanced Header with Mini Timeline
                Components\Section::make()
                    ->schema([
                        Components\Split::make([
                            Components\Grid::make(2)
                                ->schema([
                                    Components\TextEntry::make('name')
                                        ->label('Project Name')
                                        ->size(Components\TextEntry\TextEntrySize::Large)
                                        ->weight(FontWeight::Bold)
                                        ->icon('heroicon-o-rocket-launch')
                                        ->iconColor('primary')
                                        ->copyable()
                                        ->columnSpanFull(),
                
                                    Components\TextEntry::make('description')
                                        ->label('Description')
                                        ->icon('heroicon-o-document-text')
                                        ->iconColor('gray')
                                        ->columnSpanFull()
                                        ->markdown()
                                        ->prose(),
                                ]),
                
                            // Enhanced Mini Timeline Visual
                            Components\Grid::make(1)
                                ->schema([
                                    Components\Section::make('🚀 Project Timeline')
                                        ->schema([
                                            Components\TextEntry::make('timeline_display')
                                                ->label('')
                                                ->state(fn () => $this->formatTimelineDisplay())
                                                ->html()
                                                ->columnSpanFull(),
                                        ])
                                        ->compact()
                                        ->extraAttributes(['class' => 'bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-blue-950 dark:via-indigo-950 dark:to-purple-950 border-l-4 border-blue-500']),
                
                                    Components\Split::make([
                                        Components\TextEntry::make('status')
                                            ->label('Status')
                                            ->formatStateUsing(fn ($state): string => ($state instanceof ProjectState ? $state : ProjectState::tryFrom($state))?->label() ?? 'Unknown')
                                            ->badge()
                                            ->size('lg')
                                            ->color(fn ($state): string => ($state instanceof ProjectState ? $state : ProjectState::tryFrom($state))?->color() ?? 'gray')
                                            ->icon(fn ($state): string => ($state instanceof ProjectState ? $state : ProjectState::tryFrom($state))?->icon() ?? 'heroicon-o-question-mark-circle'),
                
                                        Components\TextEntry::make('priority')
                                            ->label('Priority')
                                            ->formatStateUsing(fn ($state): string => ($state instanceof ProjectPriority ? $state : ProjectPriority::tryFrom($state))?->label() ?? 'Not Set')
                                            ->badge()
                                            ->size('lg')
                                            ->color(fn ($state): string => ($state instanceof ProjectPriority ? $state : ProjectPriority::tryFrom($state))?->color() ?? 'gray')
                                            ->icon(fn ($state): string => ($state instanceof ProjectPriority ? $state : ProjectPriority::tryFrom($state))?->icon() ?? 'heroicon-o-question-mark-circle'),
                                    ]),
                
                                    // Enhanced Progress Display with Visual Bar
                                    Components\TextEntry::make('progress_percentage')
                                        ->label('Project Progress')
                                        ->formatStateUsing(function ($state) {
                                            $progress = $state ?? 0;
                                            $barColor = match(true) {
                                                $progress < 30 => 'bg-red-500',
                                                $progress < 70 => 'bg-yellow-500', 
                                                default => 'bg-green-500'
                                            };
                                            
                                            return "
                                                <div class='flex items-center gap-3'>
                                                    <div class='flex-1'>
                                                        <div class='w-full bg-gray-200 rounded-full h-3 dark:bg-gray-700'>
                                                            <div class='{$barColor} h-3 rounded-full transition-all duration-300' style='width: {$progress}%'></div>
                                                        </div>
                                                    </div>
                                                    <span class='text-sm font-semibold'>{$progress}%</span>
                                                </div>
                                            ";
                                        })
                                        ->html()
                                        ->icon(fn ($state) => match(true) {
                                            !$state || $state == 0 => 'heroicon-o-minus',
                                            $state < 30 => 'heroicon-o-exclamation-triangle',
                                            $state < 70 => 'heroicon-o-clock',
                                            default => 'heroicon-o-check-circle',
                                        }),
                                ]),
                        ])->from('md'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                // Main Details with Better Visual Organization
                Components\Section::make('📋 Project Details')
                    ->description('Core project information and key contacts')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('client.company')
                                    ->label('Client Company')
                                    ->icon('heroicon-o-building-office-2')
                                    ->iconColor('primary')
                                    ->copyable()
                                    ->formatStateUsing(fn ($record) => $record->client?->company ?: $record->client?->name ?: 'No client assigned')
                                    ->url(fn ($record) => $record->client ? \App\Filament\Resources\ClientResource::getUrl('view', ['record' => $record->client]) : null)
                                    ->openUrlInNewTab(),

                                Components\TextEntry::make('project_manager.name')
                                    ->label('Project Manager')
                                    ->icon('heroicon-o-user-circle')
                                    ->iconColor('success')
                                    ->copyable()
                                    ->default('Not assigned'),

                                Components\TextEntry::make('start_date')
                                    ->label('Start Date')
                                    ->date('M j, Y')
                                    ->icon('heroicon-o-play-circle')
                                    ->iconColor('green')
                                    ->badge()
                                    ->color('success'),

                                Components\TextEntry::make('deadline')
                                    ->label('Deadline')
                                    ->date('M j, Y')
                                    ->icon('heroicon-o-calendar-days')
                                    ->iconColor('red')
                                    ->badge()
                                    ->color(fn ($record) => $record->deadline && $record->deadline->isPast() ? 'danger' : 'warning'),
                            ]),
                    ])
                    ->collapsed(false)
                    ->columns(2),

                // Enhanced Budget Section with Better Formatting
                Components\Section::make('💰 Budget & Financial Overview')
                    ->description('Project budget allocation and spending analysis')
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('budget')
                                    ->label('Estimated Budget')
                                    ->formatStateUsing(fn ($state) => $this->formatMoney($state))
                                    ->badge()
                                    ->size('lg')
                                    ->color('success')
                                    ->icon('heroicon-o-banknotes'),

                                Components\TextEntry::make('budget_spent')
                                    ->label('Budget Spent')
                                    ->formatStateUsing(fn ($state) => $this->formatMoney($state))
                                    ->badge()
                                    ->size('lg')
                                    ->color('warning')
                                    ->icon('heroicon-o-arrow-trending-down'),

                                Components\TextEntry::make('actual_cost')
                                    ->label('Actual Cost')
                                    ->formatStateUsing(fn ($state) => $this->formatMoney($state))
                                    ->badge()
                                    ->size('lg')
                                    ->color('danger')
                                    ->icon('heroicon-o-receipt-percent'),
                            ])
                    ])
                    ->collapsed(false)
                    ->columns(3),

                // Quick Stats with Enhanced Visual Appeal
                Components\Section::make('📊 Quick Statistics')
                    ->description('Key project metrics at a glance')
                    ->schema([
                        Components\Grid::make(4)
                            ->schema([
                                Components\TextEntry::make('materials_count')
                                    ->label('Materials')
                                    ->state(fn ($record) => $record->materials()->count())
                                    ->badge()
                                    ->size('lg')
                                    ->color('info')
                                    ->icon('heroicon-o-cube'),

                                Components\TextEntry::make('documents_count')
                                    ->label('Documents')
                                    ->state(fn ($record) => $record->documents()->count())
                                    ->badge()
                                    ->size('lg')
                                    ->color('gray')
                                    ->icon('heroicon-o-document-duplicate'),

                                Components\TextEntry::make('days_remaining')
                                    ->label('Days to Deadline')
                                    ->state(function ($record) {
                                        if (!$record->deadline) return 'Not set';
                                        $days = now()->diffInDays($record->deadline, false);
                                        return $days > 0 ? $days . ' days' : ($days == 0 ? 'Today!' : abs($days) . ' overdue');
                                    })
                                    ->badge()
                                    ->size('lg')
                                    ->color(function ($record) {
                                        if (!$record->deadline) return 'gray';
                                        $days = now()->diffInDays($record->deadline, false);
                                        return $days > 7 ? 'success' : ($days > 0 ? 'warning' : 'danger');
                                    })
                                    ->icon('heroicon-o-clock'),

                                Components\TextEntry::make('completion_rate')
                                    ->label('Completion')
                                    ->state(fn ($record) => $record->progress_percentage)
                                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 1) . '%' : 'Not set')
                                    ->badge()
                                    ->size('lg')
                                    ->color(fn ($state) => match(true) {
                                        !$state || $state < 30 => 'danger',
                                        $state < 70 => 'warning',
                                        default => 'success',
                                    })
                                    ->icon('heroicon-o-chart-pie'),
                            ])
                    ])
                    ->collapsed()
                    ->columns(4),

                // Additional Info with Better Structure
                Components\Section::make('📝 Additional Information')
                    ->description('Notes and metadata')
                    ->schema([
                        Components\TextEntry::make('notes')
                            ->label('Project Notes')
                            ->markdown()
                            ->prose()
                            ->placeholder('No notes available')
                            ->columnSpanFull(),

                        Components\KeyValueEntry::make('metadata')
                            ->label('Project Metadata')
                            ->state(function ($record) {
                                $state = $record->metadata ?? null;
                                if (!is_array($state)) {
                                    return [];
                                }
                                $formatted = [];
                                foreach ($state as $key => $value) {
                                    if (is_null($value)) {
                                        $formatted[$key] = '—';
                                    } elseif (is_scalar($value)) {
                                        $formatted[$key] = (string) $value;
                                    } else {
                                        $formatted[$key] = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                    }
                                }
                                return $formatted;
                            })
                            ->columnSpanFull()
                            ->hidden(fn ($record) => empty($record->metadata)),
                    ])
                    ->collapsed()
                    ->columns(2),
            ]);
    }

    protected function formatTimelineDisplay(): string
    {
        $milestones = $this->getProjectMilestones();

        $html = '<div class="space-y-3">';
        foreach ($milestones as $milestone) {
            $statusColor = $milestone['status'] === 'completed' ? 'bg-green-500' : ($milestone['status'] === 'upcoming' ? 'bg-gray-300 dark:bg-gray-700' : 'bg-blue-500');
            $icon = $milestone['icon'];
            $label = $milestone['label'];
            $date = $milestone['date'];
            $textColor = $milestone['status'] === 'completed' ? 'text-green-700 dark:text-green-400' : ($milestone['status'] === 'upcoming' ? 'text-gray-500 dark:text-gray-400' : 'text-blue-700 dark:text-blue-400');

            $html .= "<div class='flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors'>";
            $html .= "<span class='inline-flex items-center justify-center w-8 h-8 rounded-full text-white {$statusColor} shadow-md'>";
            $html .= "<x-heroicon-o-{$icon} class='w-4 h-4' />";
            $html .= "</span>";
            $html .= "<div class='flex-1'>";
            $html .= "<div class='font-semibold {$textColor}'>{$label}</div>";
            $html .= "<div class='text-sm text-gray-500 dark:text-gray-400'>{$date}</div>";
            $html .= "</div>";
            $html .= "</div>";
        }
        $html .= '</div>';

        return $html;
    }

    protected function getProjectMilestones(): array
    {
        $record = $this->record;
        $today = now();

        return [
            [
                'label' => 'Project Start',
                'date' => optional($record->start_date)->format('M j, Y') ?? 'Not set',
                'icon' => 'play-circle',
                'status' => $record->start_date && $record->start_date->isPast() ? 'completed' : 'upcoming',
            ],
            [
                'label' => 'Current Date',
                'date' => $today->format('M j, Y'),
                'icon' => 'clock',
                'status' => 'current',
            ],
            [
                'label' => 'Deadline',
                'date' => optional($record->deadline)->format('M j, Y') ?? 'Not set',
                'icon' => 'calendar-days',
                'status' => $record->deadline && $record->deadline->isPast() ? 'completed' : 'upcoming',
            ],
            [
                'label' => 'Project End',
                'date' => optional($record->end_date)->format('M j, Y') ?? 'Not set',
                'icon' => 'flag',
                'status' => $record->end_date && $record->end_date->isPast() ? 'completed' : 'upcoming',
            ],
        ];
    }

    public function getRelationManagers(): array
    {
        return [
            \App\Filament\Resources\ProjectResource\RelationManagers\DocumentsRelationManager::class,
            \App\Filament\Resources\ProjectResource\RelationManagers\MaterialsRelationManager::class,
        ];
    }
}