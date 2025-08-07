<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    /**
     * Handle the User "deleting" event.
     */
    public function deleting(User $user): void
    {
        // Get the currently logged-in user who is performing the deletion
        $currentUser = Auth::user();
        
        if ($currentUser && $currentUser->id !== $user->id) {
            // Reassign all projects from the user being deleted to the current user
            Project::where('user_id', $user->id)
                   ->update(['user_id' => $currentUser->id]);
        } else {
            // If no current user or user is deleting themselves, 
            // find the first admin user to reassign projects to
            $adminUser = User::where('role', 'admin')
                            ->where('id', '!=', $user->id)
                            ->first();
            
            if ($adminUser) {
                Project::where('user_id', $user->id)
                       ->update(['user_id' => $adminUser->id]);
            }
        }
    }
}
