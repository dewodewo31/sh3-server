<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized');
        }

        $userRole = auth()->user()->role;
        
        // Role mappings untuk backward compatibility
        $roleMappings = [
            'admin_full_access' => 'admin',
            'admin_laman' => 'admin',
            'admin_member' => 'admin',
            'admin_bnh' => 'admin',
            'organizer' => 'organizer',
            'bendahara' => 'bendahara',
            'sponsor' => 'sponsor',
            'merchandise' => 'merchandise',
            'participant' => 'participant',
        ];
        
        $mappedRole = $roleMappings[$userRole] ?? $userRole;
        
        // FIX: Flatten comma-separated roles into a single array
        $allowedRoles = [];
        foreach ($roles as $role) {
            $allowedRoles = array_merge($allowedRoles, explode(',', $role));
        }
        $allowedRoles = array_unique(array_map('trim', $allowedRoles));
        
        $allowed = false;
        foreach ($allowedRoles as $role) {
            if ($userRole === $role || $mappedRole === $role) {
                $allowed = true;
                break;
            }
        }
        
        if (!$allowed) {
            abort(403, 'Forbidden - You don\'t have permission to access this page');
        }

        return $next($request);
    }
}