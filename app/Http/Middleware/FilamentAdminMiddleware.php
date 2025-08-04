<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class FilamentAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('filament.admin.auth.login');
        }

        $user = Auth::user();

        // Check if user can access admin panel using the FilamentUser interface method
        $adminPanel = \Filament\Facades\Filament::getPanel('admin');
        if (!$user->canAccessPanel($adminPanel)) {
            // Log the unauthorized access attempt
            logger()->warning('Unauthorized admin panel access attempt', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Redirect to unauthorized page or abort
            abort(403, 'You do not have permission to access the admin panel.');
        }

        return $next($request);
    }
}
