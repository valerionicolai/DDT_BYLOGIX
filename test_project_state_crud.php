<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Enums\ProjectState;
use App\Models\Project;
use App\Models\Client;
use App\Models\User;
use App\Services\ProjectStateService;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== PROJECT STATE ENUM CRUD OPERATIONS TEST ===\n\n";

try {
    $service = new ProjectStateService();

    // 1. READ - Get all available states
    echo "1. GETTING ALL AVAILABLE PROJECT STATES:\n";
    $states = $service->getAllStates();
    foreach ($states as $state) {
        echo "   - {$state->value}: {$state->label()} (Color: {$state->color()}, Icon: {$state->icon()})\n";
    }
    echo "\n";

    // 2. READ - Get state options for forms
    echo "2. GETTING STATE OPTIONS FOR FORMS:\n";
    $options = $service->getStateOptions();
    foreach ($options as $value => $label) {
        echo "   - {$value}: {$label}\n";
    }
    echo "\n";

    // 3. READ - Get state statistics
    echo "3. GETTING STATE STATISTICS:\n";
    $stats = $service->getStateStatistics();
    echo "   Total Projects: {$stats['total']}\n";
    echo "   Active Projects: {$stats['active_projects']}\n";
    echo "   Final Projects: {$stats['final_projects']}\n";
    echo "   Inactive Projects: {$stats['inactive_projects']}\n";
    echo "   By State:\n";
    foreach ($stats['by_state'] as $stat) {
        echo "     - {$stat['label']}: {$stat['count']} ({$stat['percentage']}%)\n";
    }
    echo "\n";

    // 4. CREATE - Create a new project with initial state
    echo "4. CREATING A NEW PROJECT WITH INITIAL STATE:\n";
    $client = Client::first();
    $user = User::first();
    
    if ($client && $user) {
        $projectData = [
            'name' => 'Test Project State Management',
            'description' => 'A test project to demonstrate state management',
            'client_id' => $client->id,
            'user_id' => $user->id,
            'priority' => 'medium',
            'budget' => 10000.00,
        ];
        
        $project = $service->createProjectWithState($projectData, ProjectState::DRAFT);
        echo "   Created project: {$project->name} with state: {$project->status->label()}\n";
        echo "\n";

        // 5. READ - Get valid transitions for the project
        echo "5. GETTING VALID TRANSITIONS FOR THE PROJECT:\n";
        $validTransitions = $service->getValidTransitions($project);
        echo "   Current state: {$project->status->label()}\n";
        echo "   Valid transitions:\n";
        foreach ($validTransitions as $transition) {
            echo "     - {$transition->value}: {$transition->label()}\n";
        }
        echo "\n";

        // 6. UPDATE - Transition project to planning state
        echo "6. TRANSITIONING PROJECT TO PLANNING STATE:\n";
        $success = $service->transitionProject($project, ProjectState::PLANNING, 'Moving to planning phase');
        if ($success) {
            $project->refresh();
            echo "   Successfully transitioned to: {$project->status->label()}\n";
        } else {
            echo "   Failed to transition project\n";
        }
        echo "\n";

        // 7. UPDATE - Transition to active state
        echo "7. TRANSITIONING PROJECT TO ACTIVE STATE:\n";
        $success = $service->transitionProject($project, ProjectState::ACTIVE, 'Project approved and started');
        if ($success) {
            $project->refresh();
            echo "   Successfully transitioned to: {$project->status->label()}\n";
        } else {
            echo "   Failed to transition project\n";
        }
        echo "\n";

        // 8. UPDATE - Try invalid transition (should fail)
        echo "8. TRYING INVALID TRANSITION (ACTIVE TO ARCHIVED):\n";
        $success = $service->transitionProject($project, ProjectState::ARCHIVED, 'Invalid transition test');
        if ($success) {
            echo "   Transition succeeded (unexpected!)\n";
        } else {
            echo "   Transition correctly rejected - invalid state change\n";
        }
        echo "\n";

        // 9. UPDATE - Transition through valid states
        echo "9. TRANSITIONING THROUGH VALID STATES:\n";
        $transitions = [
            [ProjectState::IN_PROGRESS, 'Work started'],
            [ProjectState::REVIEW, 'Work completed, under review'],
            [ProjectState::COMPLETED, 'Project completed successfully'],
        ];

        foreach ($transitions as [$newState, $reason]) {
            $success = $service->transitionProject($project, $newState, $reason);
            if ($success) {
                $project->refresh();
                echo "   Transitioned to: {$project->status->label()}\n";
            } else {
                echo "   Failed to transition to: {$newState->label()}\n";
            }
        }
        echo "\n";

        // 10. READ - Get projects by state
        echo "10. GETTING PROJECTS BY STATE (COMPLETED):\n";
        $completedProjects = $service->getProjectsByState(ProjectState::COMPLETED);
        echo "   Found {$completedProjects->count()} completed projects:\n";
        foreach ($completedProjects as $proj) {
            echo "     - {$proj->name} (Status: {$proj->status->label()})\n";
        }
        echo "\n";

        // 11. CREATE - Create multiple projects for bulk operations
        echo "11. CREATING MULTIPLE PROJECTS FOR BULK OPERATIONS:\n";
        $bulkProjects = [];
        for ($i = 1; $i <= 3; $i++) {
            $bulkProjectData = [
                'name' => "Bulk Test Project {$i}",
                'description' => "Test project {$i} for bulk operations",
                'client_id' => $client->id,
                'user_id' => $user->id,
                'priority' => 'low',
            ];
            $bulkProject = $service->createProjectWithState($bulkProjectData, ProjectState::PLANNING);
            $bulkProjects[] = $bulkProject;
            echo "   Created: {$bulkProject->name}\n";
        }
        echo "\n";

        // 12. UPDATE - Bulk transition
        echo "12. PERFORMING BULK TRANSITION TO ACTIVE STATE:\n";
        $projectIds = collect($bulkProjects)->pluck('id')->toArray();
        $results = $service->bulkTransitionProjects($projectIds, ProjectState::ACTIVE, 'Bulk activation');
        echo "   Successful transitions: " . count($results['success']) . "\n";
        echo "   Failed transitions: " . count($results['failed']) . "\n";
        echo "   Invalid transitions: " . count($results['invalid']) . "\n";
        echo "\n";

        // 13. READ - Get projects count by state
        echo "13. GETTING UPDATED PROJECTS COUNT BY STATE:\n";
        $counts = $service->getProjectsCountByState();
        foreach ($counts as $stateData) {
            echo "   {$stateData['label']}: {$stateData['count']} projects\n";
        }
        echo "\n";

        // 14. READ - Test model methods
        echo "14. TESTING PROJECT MODEL STATE METHODS:\n";
        $testProject = $bulkProjects[0];
        $testProject->refresh();
        echo "   Project: {$testProject->name}\n";
        echo "   Current state: {$testProject->status->label()}\n";
        echo "   Is active: " . ($testProject->isActive() ? 'Yes' : 'No') . "\n";
        echo "   Is final: " . ($testProject->isFinal() ? 'Yes' : 'No') . "\n";
        echo "   Is inactive: " . ($testProject->isInactive() ? 'Yes' : 'No') . "\n";
        echo "   Status color: {$testProject->status_color}\n";
        echo "   Status icon: {$testProject->status_icon}\n";
        echo "\n";

        // 15. READ - Test scopes
        echo "15. TESTING PROJECT MODEL SCOPES:\n";
        echo "   Active projects: " . Project::inActiveStates()->count() . "\n";
        echo "   Final projects: " . Project::inFinalStates()->count() . "\n";
        echo "   Inactive projects: " . Project::inInactiveStates()->count() . "\n";
        echo "   Projects by status (ACTIVE): " . Project::byStatus(ProjectState::ACTIVE)->count() . "\n";
        echo "\n";

        // 16. DELETE - Clean up test projects
        echo "16. CLEANING UP TEST PROJECTS:\n";
        $allTestProjects = Project::where('name', 'LIKE', '%Test Project%')->get();
        foreach ($allTestProjects as $proj) {
            echo "   Deleting: {$proj->name}\n";
            $proj->delete();
        }
        echo "\n";

    } else {
        echo "   Error: No clients or users found in database. Please run seeders first.\n";
    }

    echo "=== PROJECT STATE ENUM CRUD OPERATIONS TEST COMPLETED ===\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}