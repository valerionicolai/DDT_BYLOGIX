<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Enums\ProjectPriority;
use App\Models\Project;
use App\Models\Client;
use App\Services\ProjectPriorityService;
use Illuminate\Foundation\Application;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Project Priority CRUD Operations Test ===\n\n";

// Initialize the service
$priorityService = new ProjectPriorityService();

// 1. Test Priority Enum
echo "1. Testing ProjectPriority Enum:\n";
echo "Available priorities: " . implode(', ', ProjectPriority::values()) . "\n";
echo "Priority options: " . json_encode(ProjectPriority::options()) . "\n\n";

foreach (ProjectPriority::cases() as $priority) {
    echo "Priority: {$priority->value}\n";
    echo "  - Label: {$priority->label()}\n";
    echo "  - Color: {$priority->color()}\n";
    echo "  - Icon: {$priority->icon()}\n";
    echo "  - Weight: {$priority->weight()}\n";
    echo "  - Description: {$priority->description()}\n";
    echo "  - SLA Hours: {$priority->slaHours()}\n";
    echo "  - Is High Priority: " . (in_array($priority, [ProjectPriority::HIGH, ProjectPriority::URGENT, ProjectPriority::CRITICAL]) ? 'Yes' : 'No') . "\n";
    echo "  - Needs Immediate Attention: " . (in_array($priority, [ProjectPriority::URGENT, ProjectPriority::CRITICAL]) ? 'Yes' : 'No') . "\n\n";
}

// 2. Create test client and user if not exists
$client = Client::firstOrCreate([
    'name' => 'Test Client for Priority',
    'email' => 'priority@test.com',
    'phone' => '123-456-7890'
]);

$user = \App\Models\User::firstOrCreate([
    'email' => 'priority-test@example.com'
], [
    'name' => 'Priority Test User',
    'password' => bcrypt('password'),
    'role' => 'admin'
]);

// 3. Create projects with different priorities
echo "2. Creating projects with different priorities:\n";
$projects = [];

foreach (ProjectPriority::cases() as $priority) {
    $project = Project::create([
        'name' => "Test Project - {$priority->label()}",
        'description' => "Test project with {$priority->label()} priority",
        'client_id' => $client->id,
        'user_id' => $user->id,
        'priority' => $priority,
        'status' => 'active',
        'start_date' => now(),
        'end_date' => now()->addDays(30),
        'budget' => 10000,
        'progress_percentage' => rand(10, 90)
    ]);
    
    $projects[] = $project;
    echo "Created project: {$project->name} with priority: {$project->priority->label()}\n";
}
echo "\n";

// 4. Test priority service methods
echo "3. Testing Priority Service Methods:\n";

// Get all priorities
$allPriorities = $priorityService->getAllPriorities();
$priorityValues = array_map(fn($p) => $p->value, $allPriorities);
echo "All priorities: " . implode(', ', $priorityValues) . "\n";

// Get priority options
$options = $priorityService->getPriorityOptions();
echo "Priority options: " . json_encode($options) . "\n";

// Get priority statistics
$stats = $priorityService->getPriorityStatistics();
echo "Priority statistics:\n";
echo "  Total projects: {$stats['total']}\n";
echo "  High priority projects: {$stats['high_priority_projects']}\n";
echo "  Low priority projects: {$stats['low_priority_projects']}\n";
echo "  Urgent projects: {$stats['urgent_projects']}\n";
echo "  By priority:\n";
foreach ($stats['by_priority'] as $priorityData) {
    echo "    {$priorityData['label']}: {$priorityData['count']} projects ({$priorityData['percentage']}%)\n";
}
echo "\n";

// 5. Test project priority methods
echo "4. Testing Project Priority Methods:\n";
$testProject = $projects[0];

echo "Test project: {$testProject->name}\n";
echo "Current priority: {$testProject->priority->label()}\n";
echo "Priority color: {$testProject->priority->color()}\n";
echo "Priority icon: {$testProject->priority->icon()}\n";
echo "Priority weight: {$testProject->priority->weight()}\n";
echo "Priority description: {$testProject->priority->description()}\n";
echo "SLA hours: {$testProject->priority->slaHours()}\n";
echo "Is high priority: " . (in_array($testProject->priority, [ProjectPriority::HIGH, ProjectPriority::URGENT, ProjectPriority::CRITICAL]) ? 'Yes' : 'No') . "\n";
echo "Is low priority: " . ($testProject->priority === ProjectPriority::LOW ? 'Yes' : 'No') . "\n";
echo "Needs immediate attention: " . (in_array($testProject->priority, [ProjectPriority::URGENT, ProjectPriority::CRITICAL]) ? 'Yes' : 'No') . "\n";
echo "Can escalate: " . ($testProject->priority !== ProjectPriority::CRITICAL ? 'Yes' : 'No') . "\n";
echo "Can de-escalate: " . ($testProject->priority !== ProjectPriority::LOW ? 'Yes' : 'No') . "\n\n";

// 6. Test priority escalation/de-escalation
echo "5. Testing Priority Escalation/De-escalation:\n";
$escalationProject = $projects[1]; // Medium priority project

echo "Original priority: {$escalationProject->priority->label()}\n";

if ($escalationProject->priority !== ProjectPriority::CRITICAL) {
    $priorityService->escalatePriority($escalationProject);
    $escalationProject->refresh();
    echo "After escalation: {$escalationProject->priority->label()}\n";
}

if ($escalationProject->priority !== ProjectPriority::LOW) {
    $priorityService->deEscalatePriority($escalationProject);
    $escalationProject->refresh();
    echo "After de-escalation: {$escalationProject->priority->label()}\n";
}

// Test setting specific priority
$priorityService->changePriority($escalationProject, ProjectPriority::URGENT);
$escalationProject->refresh();
echo "After setting to urgent: {$escalationProject->priority->label()}\n\n";

// 7. Test priority service operations
echo "6. Testing Priority Service Operations:\n";

// Change priority
$changeResult = $priorityService->changePriority($testProject, ProjectPriority::HIGH, 'Testing priority change');
echo "Priority change result: " . ($changeResult ? 'Success' : 'Failed') . "\n";

// Escalate priority
$escalateResult = $priorityService->escalatePriority($testProject, 'Testing escalation');
echo "Priority escalation result: " . ($escalateResult ? 'Success' : 'Failed') . "\n";

// De-escalate priority
$deEscalateResult = $priorityService->deEscalatePriority($testProject, 'Testing de-escalation');
echo "Priority de-escalation result: " . ($deEscalateResult ? 'Success' : 'Failed') . "\n";

// Bulk change priority
$projectIds = array_slice(array_column($projects, 'id'), 0, 3);
$bulkResult = $priorityService->bulkChangePriority($projectIds, ProjectPriority::MEDIUM, 'Bulk testing');
echo "Bulk priority change - Success: " . count($bulkResult['success']) . ", Failed: " . count($bulkResult['failed']) . "\n\n";

// 8. Test priority queries and scopes
echo "7. Testing Priority Queries and Scopes:\n";

// Get projects by priority
$highPriorityProjects = $priorityService->getProjectsByPriority(ProjectPriority::HIGH);
echo "High priority projects: " . $highPriorityProjects->count() . "\n";

$urgentProjects = $priorityService->getUrgentProjects();
echo "Urgent projects: " . $urgentProjects->count() . "\n";

$highPriorityAndAbove = $priorityService->getHighPriorityProjects();
echo "High priority and above projects: " . $highPriorityAndAbove->count() . "\n";

// Test scopes
$lowPriorityCount = Project::lowPriority()->count();
echo "Low priority projects (scope): {$lowPriorityCount}\n";

$highPriorityCount = Project::highPriority()->count();
echo "High priority projects (scope): {$highPriorityCount}\n";

$urgentPriorityCount = Project::urgentPriority()->count();
echo "Urgent priority projects (scope): {$urgentPriorityCount}\n";

// Test ordered by priority
$orderedProjects = Project::orderByPriority()->get();
echo "Projects ordered by priority:\n";
foreach ($orderedProjects as $project) {
    echo "  - {$project->name}: {$project->priority->label()}\n";
}
echo "\n";

// 9. Test priority counts by priority
echo "8. Testing Priority Counts:\n";
foreach (ProjectPriority::cases() as $priority) {
    $count = $priorityService->getProjectsByPriority($priority)->count();
    echo "Priority {$priority->label()}: {$count} projects\n";
}
echo "\n";

// 10. Test projects needing escalation (simulate overdue projects)
echo "9. Testing Projects Needing Escalation:\n";

// Make some projects overdue
$overdueProject = $projects[0];
$overdueProject->update(['end_date' => now()->subDays(5)]);

$needingEscalation = $priorityService->getProjectsNeedingEscalation();
echo "Projects needing escalation: " . $needingEscalation->count() . "\n";

// Auto-escalate overdue projects
$escalationResults = $priorityService->autoEscalateProjects();
echo "Auto-escalated projects: " . count($escalationResults['escalated']) . "\n";
echo "Already at max priority: " . count($escalationResults['already_max']) . "\n";
echo "Failed to escalate: " . count($escalationResults['failed']) . "\n\n";

// 11. Test priority comparison methods
echo "10. Testing Priority Comparison Methods:\n";
$priority1 = ProjectPriority::LOW;
$priority2 = ProjectPriority::HIGH;

echo "Comparing {$priority1->label()} vs {$priority2->label()}:\n";
echo "  - Is higher: " . ($priority1->isHigherThan($priority2) ? 'Yes' : 'No') . "\n";
echo "  - Is lower: " . ($priority1->isLowerThan($priority2) ? 'Yes' : 'No') . "\n";
echo "  - Is equal: " . ($priority1 === $priority2 ? 'Yes' : 'No') . "\n";

$escalated = $priority1->escalate();
echo "  - {$priority1->label()} escalated to: {$escalated->label()}\n";

$deEscalated = $priority2->deEscalate();
echo "  - {$priority2->label()} de-escalated to: {$deEscalated->label()}\n\n";

// 12. Clean up test data
echo "11. Cleaning up test data:\n";
foreach ($projects as $project) {
    $project->delete();
    echo "Deleted project: {$project->name}\n";
}

$client->delete();
echo "Deleted test client\n";

$user->delete();
echo "Deleted test user\n";

echo "\n=== Project Priority CRUD Operations Test Complete ===\n";