<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Project;
use App\Models\Document;
use Illuminate\Support\Facades\Cache;

class RecentActivity extends Widget
{
    protected static string $view = 'filament.widgets.recent-activity';
    
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = [
        'sm' => 2,
        'md' => 2,
        'lg' => 2,
        'xl' => 2,
    ];

    public function getViewData(): array
    {
        return [
            'activities' => $this->getRecentActivities(),
        ];
    }

    protected function getRecentActivities()
    {
        return Cache::remember('recent_activities', 300, function () {
            $activities = collect();

            // Get recent projects
            try {
                $recentProjects = Project::with('client', 'user')
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(function ($project) {
                        return [
                            'id' => $project->id,
                            'type' => 'project',
                            'icon' => 'heroicon-o-folder',
                            'color' => 'primary',
                            'title' => "Project: {$project->name}",
                            'description' => 'Client: ' . ($project->client->name ?? 'N/A') . ' | Status: ' . ucfirst($project->status),
                            'user_name' => $project->user->name ?? 'System',
                            'created_at' => $project->created_at,
                            'url' => '#',
                        ];
                    });
                $activities = $activities->concat($recentProjects);
            } catch (\Exception $e) {
                // Handle case where projects table doesn't exist yet
            }

            // Get recent documents
            try {
                $recentDocuments = Document::with('project', 'user')
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(function ($document) {
                        return [
                            'id' => $document->id,
                            'type' => 'document',
                            'icon' => 'heroicon-o-document',
                            'color' => 'success',
                            'title' => "Document: {$document->name}",
                            'description' => 'Project: ' . ($document->project->name ?? 'N/A') . ' | Type: ' . ucfirst($document->type ?? 'file'),
                            'user_name' => $document->user->name ?? 'System',
                            'created_at' => $document->created_at,
                            'url' => '#',
                        ];
                    });
                $activities = $activities->concat($recentDocuments);
            } catch (\Exception $e) {
                // Handle case where documents table doesn't exist yet
            }

            // Sort by created_at and take the most recent 10
            return $activities->sortByDesc('created_at')->take(10)->values();
        });
    }

    public function getPollingInterval(): ?string
    {
        return '30s';
    }
}