<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsTeacher
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Load roles relationship
        if (!$user->relationLoaded('roles')) {
            $user->load('roles');
        }

        if (!$user->hasRole('teacher') && !$user->hasRole('admin')) {
            abort(403, 'Unauthorized access. Teacher privileges required.');
        }

        return $next($request);
    }
}
