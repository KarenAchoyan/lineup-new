<?php

namespace App\Http\Middleware;

use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        
        // Load roles if user is authenticated
        if ($user) {
            if (!$user->relationLoaded('roles')) {
                $user->load('roles');
            }
        }
        
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles->map(function ($role) {
                        return [
                            'id' => $role->id,
                            'name' => $role->name,
                            'slug' => $role->slug,
                        ];
                    }),
                    'is_admin' => $user->isAdmin(),
                    'is_teacher' => $user->isTeacher(),
                ] : null,
            ],
            'ziggy' => function () use ($request) {
                return array_merge((new Ziggy)->toArray(), [
                    'location' => $request->url(),
                ]);
            },
            'locale' => app()->getLocale(),
            'translations' => function () {
                return trans('app');
            },
            'availableLocales' => ['en', 'hy', 'ge', 'ru'],
            'courses' => function () {
                return Course::active()
                    ->ordered()
                    ->get()
                    ->map(function ($course) {
                        return [
                            'id' => $course->id,
                            'name' => $course->getName(app()->getLocale()),
                            'slug' => $course->slug,
                        ];
                    });
            },
        ]);
    }
}
