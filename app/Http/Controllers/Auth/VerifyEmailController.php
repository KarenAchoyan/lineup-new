<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        
        if ($user->hasVerifiedEmail()) {
            return $this->redirectBasedOnRole($user);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return $this->redirectBasedOnRole($user);
    }

    /**
     * Redirect user based on their role.
     */
    private function redirectBasedOnRole($user): RedirectResponse
    {
        // Load roles relationship
        if (!$user->relationLoaded('roles')) {
            $user->load('roles');
        }

        // Redirect based on role
        if ($user->hasRole('admin')) {
            return redirect()->intended(route('dashboard').'?verified=1');
        }
        
        if ($user->hasRole('teacher')) {
            return redirect()->intended(route('dashboard').'?verified=1');
        }

        return redirect()->intended(route('profile.edit').'?verified=1');
    }
}
