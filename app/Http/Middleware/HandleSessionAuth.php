<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HandleSessionAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Se la richiesta proviene da un dominio stateful e non ha un token Bearer,
        // prova ad autenticare usando la sessione web
        if ($this->isStatefulRequest($request) && !$request->bearerToken()) {
            // Forza l'uso del guard web per le richieste stateful
            Auth::shouldUse('web');
        }

        return $next($request);
    }

    /**
     * Determina se la richiesta proviene da un dominio stateful
     */
    protected function isStatefulRequest(Request $request): bool
    {
        $statefulDomains = config('sanctum.stateful', []);
        
        foreach ($statefulDomains as $domain) {
            if ($request->getHost() === $domain || 
                $request->getHttpHost() === $domain ||
                str_contains($request->getHttpHost(), $domain)) {
                return true;
            }
        }

        return false;
    }
}