<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                /** @var \App\Models\User $user */
                $user = Auth::guard($guard)->user();
                
                // Load roles relationship
                if (!$user->relationLoaded('roles')) {
                    $user->load('roles');
                }
                
                // Redirect based on role
                if ($user->hasRole('admin')) {
                    return redirect(route('dashboard'));
                }
                
                if ($user->hasRole('teacher')) {
                    return redirect(route('teacher.groups.index'));
                }
                
                return redirect(route('profile.edit'));
            }
        }

        return $next($request);
    }
}
